#!/bin/bash

# Fix Laravel storage permissions
# Run this script with: sudo bash fix-permissions.sh

cd /home/greentech/www/compenzations

echo "Fixing ownership and permissions for Laravel storage directories..."

# Change ownership to greentech user
chown -R greentech:greentech storage bootstrap/cache

# Set proper permissions
chmod -R 775 storage bootstrap/cache

# Set ACL permissions to allow both greentech and www-data to write
# This is the best approach for Laravel applications
setfacl -R -m u:www-data:rwX storage bootstrap/cache
setfacl -R -d -m u:www-data:rwX storage bootstrap/cache
setfacl -R -m u:greentech:rwX storage bootstrap/cache
setfacl -R -d -m u:greentech:rwX storage bootstrap/cache

echo "Done! Permissions fixed."
echo ""
echo "Ownership:"
ls -ld storage
echo ""
echo "Permissions:"
ls -la storage/

