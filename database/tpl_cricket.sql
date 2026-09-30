CREATE DATABASE IF NOT EXISTS tpl_cricket
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tpl_cricket;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'Player',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    venue VARCHAR(150) DEFAULT '',
    match_date DATETIME NOT NULL,
    status ENUM('scheduled','live','completed') DEFAULT 'scheduled',
    current_innings INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team1_id) REFERENCES teams(id),
    FOREIGN KEY (team2_id) REFERENCES teams(id)
);

CREATE TABLE innings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    innings_number INT NOT NULL,
    batting_team_id INT NOT NULL,
    runs INT DEFAULT 0,
    wickets INT DEFAULT 0,
    balls INT DEFAULT 0,
    target INT DEFAULT NULL,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (batting_team_id) REFERENCES teams(id)
);

CREATE TABLE balls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    innings_id INT NOT NULL,
    ball_number INT NOT NULL,
    runs INT DEFAULT 0,
    extra_runs INT DEFAULT 0,
    extra_type VARCHAR(20) DEFAULT NULL,
    wicket TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (innings_id) REFERENCES innings(id) ON DELETE CASCADE
);

INSERT INTO admins (username, password)
VALUES ('admin', SHA2('admin123', 256));

INSERT INTO teams (name, short_name) VALUES
('Kathmandu Team', 'KTM'),
('Pokhara Team', 'PKR');

INSERT INTO matches (team1_id, team2_id, venue, match_date, status)
VALUES (1, 2, 'TU Cricket Ground, Kirtipur', NOW(), 'scheduled');
