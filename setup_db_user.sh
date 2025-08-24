#!/bin/bash

# Regina Hotel - Database User Setup Script

echo "=== Regina Hotel Database User Setup ==="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "Please run this script as root (sudo)"
    exit 1
fi

# Database credentials
DB_NAME="regina_hotel"
DB_USER="hotel_admin"
DB_PASS="passwordku123"

echo "Creating database user: $DB_USER"
echo "Database: $DB_NAME"
echo ""

# Create MySQL user and grant privileges
mysql -u root -p << EOF
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SHOW GRANTS FOR '$DB_USER'@'localhost';
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Database user '$DB_USER' created successfully!"
    echo ""
    echo "Database credentials:"
    echo "  Host: localhost"
    echo "  Database: $DB_NAME"
    echo "  Username: $DB_USER"
    echo "  Password: $DB_PASS"
    echo ""
    echo "You can now use these credentials in your application."
else
    echo ""
    echo "❌ Failed to create database user."
    echo "Please check your MySQL root credentials and try again."
fi
