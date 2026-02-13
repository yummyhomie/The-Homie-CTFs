#!/bin/sh
set -eu


DB_DIR="/var/www/html/database"

# CREATE TABLES
echo "CREATING TABLES..."
sqlite3 "$DB_DIR/users.db" <<'SQL'
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT UNIQUE,
  password TEXT
);
SQL

sqlite3 "$DB_DIR/comments.db" <<'SQL'
CREATE TABLE IF NOT EXISTS comments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  session_id TEXT,
  username TEXT DEFAULT 'Anonymous',
  comment TEXT NOT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
SQL

# INSERT DATA
echo "INSERTING USERS..."
sqlite3 "$DB_DIR/users.db" <<'SQL'
INSERT INTO users (username, password) VALUES
  ('Admin', 'bruhifyouguessedthisyoureactuallyinsanelikeforreal'),
  ('HABOOGA', 'password1'),
  ('Leond', 'password3'),
  ('zekriv', 'password2');
SQL

echo "INSERTING COMMENTS..."
sqlite3 "$DB_DIR/comments.db" <<'SQL'
INSERT INTO comments (session_id, username, comment, timestamp) VALUES
  ('static_session', 'HABOOGA', 'NixOS sucks, you should use Zorin OS', '2001-11-15 14:31:57'),
  ('static_session', 'Admin', 'I will bomb your house. ☝️', '2001-11-16 09:15:23'),
  ('static_session', 'Leond', 'If a youtuber like Pewdiepie can use Arch Linux, what is stopping you?', '2001-11-17 05:21:39'),
  ('static_session', 'zekriv', 'It wasnt too long ago when I decided to jump ship and begin my commitment to the Free and Open Source Software world. It seems like a daunting task at first, but quickly the power of control I gained felt incredible. If you''re on the fence of using Linux as your main daily driver, this is is a sign for you to just do it. Also, I use arch btw', '2001-11-17 17:35:47');
SQL

# SET PROPER OWNERSHIP AND PERMISSIONS FOR DB FILES
# Use numeric IDs that match www-data (33:33 is standard)
echo "Setting ownership and permissions..."
chown -R www-data:www-data "$DB_DIR"
chmod -R 775 "$DB_DIR"
chmod 664 "$DB_DIR"/*.db || true

echo "DATA INSERTED PROPERLY."
echo "DATABASE CREATED AND ACCESSIBLE."
echo "HAPPY HACKING! GO HACK THE PLANET!"