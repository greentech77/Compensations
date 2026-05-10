#!/bin/bash

# Fix Laravel storage / bootstrap-cache / mPDF temp permissions for this
# Apache deployment.
#
#   Run with: sudo bash fix-permissions.sh
#
# Detects the actual Apache runtime user from /etc/apache2/envvars (falling
# back to www-data, which is the Debian/Ubuntu default) and aligns ownership
# of every directory PHP/Laravel needs to write into. Without this, you'll
# typically see one of these errors:
#   - "Please provide a valid cache path" (Blade compiler can't write)
#   - "tempnam(): file created in the system's temporary directory" (Filesystem::replace)
#   - "Temporary files directory ... is not writable" (mPDF)
#   - silent PDF generation failures inside the AddCompenzationEvent listener

set -euo pipefail

APP_ROOT="/var/www/html/compenzations"

if [[ "$EUID" -ne 0 ]]; then
    echo "[fix-permissions] this script needs to run as root (sudo bash $0)" >&2
    exit 1
fi

if [[ ! -d "$APP_ROOT" ]]; then
    echo "[fix-permissions] $APP_ROOT does not exist" >&2
    exit 1
fi

# Resolve the Apache runtime user. Default to www-data if not detectable.
# /etc/apache2/envvars references variables like $APACHE_CONFDIR that are
# only set by the systemd unit, so we need to disable `set -u` while sourcing
# it (and provide a benign default for $APACHE_CONFDIR so the script doesn't
# blow up before it ever gets to the chown).
APACHE_USER="www-data"
APACHE_GROUP="www-data"
if [[ -r /etc/apache2/envvars ]]; then
    set +u
    APACHE_CONFDIR="${APACHE_CONFDIR:-/etc/apache2}"
    APACHE_RUN_USER=""
    APACHE_RUN_GROUP=""
    # shellcheck disable=SC1091
    source /etc/apache2/envvars || true
    set -u
    APACHE_USER="${APACHE_RUN_USER:-$APACHE_USER}"
    APACHE_GROUP="${APACHE_RUN_GROUP:-$APACHE_GROUP}"
fi

echo "[fix-permissions] target user:  $APACHE_USER"
echo "[fix-permissions] target group: $APACHE_GROUP"
echo "[fix-permissions] app root:     $APP_ROOT"
echo

cd "$APP_ROOT"

# Make sure the directories we expect actually exist before chmod-ing them,
# so a missing path doesn't break the whole script.
mkdir -p \
    storage/app/mpdf-tmp \
    storage/app/proposals \
    storage/app/agreements/implementation \
    storage/app/agreements/realization \
    storage/app/bills \
    storage/app/exports \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    vendor/mpdf/mpdf/tmp/mpdf

TARGETS=(
    storage
    bootstrap/cache
    vendor/mpdf/mpdf/tmp
)

for target in "${TARGETS[@]}"; do
    if [[ -e "$target" ]]; then
        echo "[fix-permissions] chown $APACHE_USER:$APACHE_GROUP $target"
        chown -R "$APACHE_USER:$APACHE_GROUP" "$target"
        find "$target" -type d -exec chmod 0775 {} +
        find "$target" -type f -exec chmod 0664 {} +
    fi
done

# Re-set the Laravel .gitignore files to executable bits matching the rest of
# the tree (some prior chmod runs left them as 0664 which is fine, but if
# they were 0644 root-owned from a sudo install we want them aligned).
chmod 0664 storage/.gitignore 2>/dev/null || true
chmod 0664 storage/app/.gitignore 2>/dev/null || true
chmod 0664 storage/framework/.gitignore 2>/dev/null || true
chmod 0664 storage/logs/.gitignore 2>/dev/null || true
chmod 0664 bootstrap/cache/.gitignore 2>/dev/null || true

echo
echo "[fix-permissions] Done. Verifying writeability:"
echo
for d in storage storage/framework/views storage/app bootstrap/cache vendor/mpdf/mpdf/tmp; do
    if sudo -u "$APACHE_USER" -- test -w "$APP_ROOT/$d"; then
        echo "  OK    $d (writable by $APACHE_USER)"
    else
        echo "  FAIL  $d (NOT writable by $APACHE_USER)"
    fi
done

echo
echo "[fix-permissions] Reload Apache so PHP workers pick up fresh realpath/OPcache:"
echo "    sudo systemctl reload apache2"
