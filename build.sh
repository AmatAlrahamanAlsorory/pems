#!/bin/bash
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Install Node dependencies and build assets
npm install --production
npm run build

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Cache config for production
php artisan config:cache