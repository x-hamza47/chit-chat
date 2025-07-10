CREATE DATABASE chat_app;

USE chat_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_id BIGINT NOT NULL,
    fname VARCHAR(100) NOT NULL,
    lname VARCHAR(100) NOT NULL,
    uname VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    img VARCHAR(255),
    status ENUM('active', 'offline')
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incoming_msg_id BIGINT NOT NULL,
    outgoing_msg_id BIGINT NOT NULL,
    msg TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE messages ADD COLUMN created_at TIMESTAMP AFTER msg;
ALTER TABLE messages MODIFY created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE messages ADD COLUMN is_read BOOLEAN DEFAULT false AFTER msg;