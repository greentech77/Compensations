#!/bin/bash

# Script to fix database connection settings

cd ~/www/compenzations

echo "Fixing database connection in .env..."
echo ""

# Check if MySQL socket exists
SOCKET=$(find /var/run/mysqld /var/lib/mysql /tmp -name "mysql.sock" 2>/dev/null | head -1)

if [ -n "$SOCKET" ]; then
    echo "Found MySQL socket: $SOCKET"
    # Use unix socket connection instead of TCP
    sed -i 's|^DB_HOST=.*|DB_HOST=/var/run/mysqld/mysqld.sock|' .env
    # For socket, use 'unix_socket' or keep localhost but set unix_socket in config
    echo "Updated DB_HOST to use socket connection"
else
    echo "No socket found, checking alternative hosts..."
    
    # Try common configurations
    echo "Trying different connection methods..."
    
    # Option 1: localhost instead of 127.0.0.1
    sed -i 's|^DB_HOST=127\.0\.0\.1|DB_HOST=localhost|' .env
    
    echo "Changed DB_HOST to localhost"
fi

# Display current config
echo ""
echo "Current database configuration:"
grep "^DB_" .env

echo ""
echo "Test connection with: php artisan migrate:status"

