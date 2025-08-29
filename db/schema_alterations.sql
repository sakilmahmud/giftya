-- Alter 'customers' table: Add a 'password' column
-- IMPORTANT: VARCHAR(255) is common for hashed passwords.
-- Adjust 'NOT NULL DEFAULT ''' if your requirements for existing customer records differ.
ALTER TABLE customers ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER `phone`;