#!/bin/bash

# Regina Hotel Database Setup Script

echo "=== Regina Hotel Management System - Database Setup ==="
echo ""

# Database configuration
DB_HOST="localhost"
DB_USER="root"
DB_PASS=""
DB_NAME="regina_hotel"

# Check if MySQL is running
if ! command -v mysql &> /dev/null; then
    echo "Error: MySQL is not installed or not in PATH"
    exit 1
fi

echo "1. Creating database and tables..."
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS < database/setup.sql

if [ $? -eq 0 ]; then
    echo "✓ Database setup completed successfully!"
else
    echo "✗ Database setup failed!"
    exit 1
fi

echo ""
echo "2. Testing database connection..."

# Test PHP database connection
php -r "
require_once 'config/database.php';
try {
    \$db = new Database();
    \$result = \$db->fetchOne('SELECT COUNT(*) as count FROM users');
    echo '✓ Database connection successful!\\n';
    echo '✓ Found ' . \$result['count'] . ' users in database\\n';
} catch (Exception \$e) {
    echo '✗ Database connection failed: ' . \$e->getMessage() . '\\n';
    exit(1);
}
"

if [ $? -eq 0 ]; then
    echo ""
    echo "=== Setup Summary ==="
    echo "✓ Database: $DB_NAME"
    echo "✓ Default users created:"
    echo "  - Owner: owner / admin123"
    echo "  - Admin: admin / admin123" 
    echo "  - Receptionist: receptionist / admin123"
    echo "✓ Sample rooms and floors created"
    echo ""
    echo "Your Regina Hotel Management System is ready!"
    echo "Access it via: http://localhost/reginahotel/"
else
    echo "✗ Setup verification failed!"
    exit 1
fi
