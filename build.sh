#!/bin/bash
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
npm install
npm run build

# Create SQLite database if it doesn't exist
touch database/database.sqlite

# Set permissions
chmod 755 storage/logs
chmod 755 bootstrap/cache