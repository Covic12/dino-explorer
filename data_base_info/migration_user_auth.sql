USE dinosaur_db;

ALTER TABLE user 
ADD COLUMN password VARCHAR(255) NOT NULL AFTER email,
ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user' NOT NULL AFTER password;

CREATE INDEX idx_username ON user(username);
CREATE INDEX idx_email ON user(email);
