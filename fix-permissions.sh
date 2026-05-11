#!/bin/bash

# Fix Laravel storage / bootstrap-cache / mPDF temp permissions.
#
#   Run with: sudo bash fix-permissions.sh
#
# Detects the web-server runtime user (Apache via /etc/apache2/envvars, or
# nginx/php-fpm via /etc/php/*/fpm/pool.d/*.conf), falling back to www-data.
# Aligns ownership of every directory PHP/Laravel needs to write into.
#
# IMPORTANT: Run this after any `php artisan` command that writes to storage/
# (e.g. compenzations:regenerate-pdfs), because artisan runs as root and
# creates files owned by root that the web-server user cannot later overwrite.
#
# Without this, you may see:
#   - "Please provide a valid cache path" (Blade compiler can't write)
#   - "tempnam(): file created in the system's temporary directory"
#   - "Temporary files directory ... is not writable" (mPDF)
#   - silent PDF generation failures inside the AddCompenzationEvent listener
#     (Storage::put() returns false when writing to root-owned PDF files)

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

# Resolve the web-server runtime user.
# 1. Try Apache (/etc/apache2/envvars).
# 2. Try PHP-FPM pool config for nginx (look for user= lines).
# 3. Fall back to www-data.
WEB_USER="www-data"
WEB_GROUP="www-data"

if [[ -r /etc/apache2/envvars ]]; then
    set +u
    APACHE_CONFDIR="${APACHE_CONFDIR:-/etc/apache2}"
    APACHE_RUN_USER=""
    APACHE_RUN_GROUP=""
    # shellcheck disable=SC1091
    source /etc/apache2/envvars || true
    set -u
    WEB_USER="${APACHE_RUN_USER:-$WEB_USER}"
    WEB_GROUP="${APACHE_RUN_GROUP:-$WEB_GROUP}"
elif [[ -d /etc/php ]]; then
    # nginx + php-fpm: read user from the first pool config found
    FPM_CONF=$(find /etc/php -name "*.conf" -path "*/pool.d/*" 2>/dev/null | head -1)
    if [[ -n "$FPM_CONF" ]]; then
        _U=$(grep -E '^user\s*=' "$FPM_CONF" 2>/dev/null | head -1 | sed 's/.*=\s*//' | tr -d '[:space:]')
        _G=$(grep -E '^group\s*=' "$FPM_CONF" 2>/dev/null | head -1 | sed 's/.*=\s*//' | tr -d '[:space:]')
        WEB_USER="${_U:-$WEB_USER}"
        WEB_GROUP="${_G:-$WEB_GROUP}"
    fi
fi

# Backward-compat aliases (old variable names referenced in the chown loop below)
APACHE_USER="$WEB_USER"
APACHE_GROUP="$WEB_GROUP"

echo "[fix-permissions] target user:  $WEB_USER"
echo "[fix-permissions] target group: $WEB_GROUP"
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
