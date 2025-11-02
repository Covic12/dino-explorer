CREATE DATABASE IF NOT EXISTS dinosaur_db;
USE dinosaur_db;

CREATE TABLE eras (
    era_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    era VARCHAR(100),
    period VARCHAR(100)
);

CREATE TABLE location (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    picture VARCHAR(255),
    continent VARCHAR(100),
    country VARCHAR(100),
    location_url TEXT
);

CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    registration_date DATE NOT NULL
);

CREATE TABLE researchers (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    picture VARCHAR(255),
    description TEXT,
    discoveries TEXT
);

CREATE TABLE dinosaurs (
    dinosaur_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    diet VARCHAR(50),
    size VARCHAR(50),
    weight VARCHAR(50),
    description TEXT,
    era_id INT,
    location_id INT,
    CONSTRAINT fk_dinosaur_era FOREIGN KEY (era_id)
        REFERENCES eras(era_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_dinosaur_location FOREIGN KEY (location_id)
        REFERENCES location(location_id)
        ON DELETE SET NULL ON UPDATE CASCADE
);
