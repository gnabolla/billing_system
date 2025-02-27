-- Create the subscriber_auth table for subscriber authentication
CREATE TABLE IF NOT EXISTS subscriber_auth (
    subscriber_auth_id   INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id        INT NOT NULL,
    password_hash        VARCHAR(255) NOT NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires DATETIME NULL,
    last_login           DATETIME NULL,
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscriber_id) REFERENCES subscribers(subscriber_id),
    UNIQUE (subscriber_id)
);

-- Index for more efficient searching
CREATE INDEX idx_subscriber_id ON subscriber_auth(subscriber_id);
CREATE INDEX idx_password_reset_token ON subscriber_auth(password_reset_token);