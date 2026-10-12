-- New demo reconstruction inferred from PHP, not the missing original SQL dump.
CREATE DATABASE IF NOT EXISTS poppleton CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE poppleton;
CREATE TABLE owners (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL, phone VARCHAR(30), address VARCHAR(255));
CREATE TABLE breeds (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL UNIQUE);
CREATE TABLE dogs (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL, breed_id INT NOT NULL, owner_id INT NOT NULL, FOREIGN KEY (breed_id) REFERENCES breeds(id), FOREIGN KEY (owner_id) REFERENCES owners(id));
CREATE TABLE events (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(120) NOT NULL, event_date DATE NOT NULL);
CREATE TABLE competitions (id INT PRIMARY KEY AUTO_INCREMENT, event_id INT NOT NULL, name VARCHAR(120) NOT NULL, FOREIGN KEY (event_id) REFERENCES events(id));
CREATE TABLE entries (id INT PRIMARY KEY AUTO_INCREMENT, dog_id INT NOT NULL, competition_id INT NOT NULL, score DECIMAL(6,2) NOT NULL, UNIQUE (dog_id, competition_id), FOREIGN KEY (dog_id) REFERENCES dogs(id), FOREIGN KEY (competition_id) REFERENCES competitions(id));
CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(100) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, role ENUM('admin', 'user') NOT NULL DEFAULT 'user');
