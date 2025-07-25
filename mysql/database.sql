CREATE DATABASE chat_app;

USE chat_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_id CHAR(36) NOT NULL UNIQUE,
    fname VARCHAR(100) NOT NULL,
    lname VARCHAR(100) NOT NULL,
    uname VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    img VARCHAR(255),
    status ENUM('active', 'offline')
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incoming_msg_id CHAR(36) NOT NULL,
    outgoing_msg_id CHAR(36) NOT NULL,
    msg TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE messages ADD COLUMN created_at TIMESTAMP AFTER msg;
ALTER TABLE messages MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE messages ADD COLUMN is_read BOOLEAN DEFAULT false AFTER msg;

ALTER TABLE users 
    MODIFY unique_id CHAR(36) NOT NULL,
    ADD UNIQUE (unique_id);

-- ALTER TABLE users 
--     MODIFY unique_id BINARY(16) NOT NULL;

ALTER TABLE messages
    MODIFY incoming_msg_id CHAR(36) NOT NULL,
    MODIFY outgoing_msg_id CHAR(36) NOT NULL;

-- ALTER TABLE messages 
--     MODIFY incoming_msg_id BINARY(16) NOT NULL,
--     MODIFY outgoing_msg_id BINARY(16) NOT NULL;

CREATE INDEX idx_incoming ON messages (incoming_msg_id);
CREATE INDEX idx_outgoing ON messages (outgoing_msg_id);