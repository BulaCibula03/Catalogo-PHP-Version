CREATE DATABASE IF NOT EXISTS catalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE catalog;

-- -------------------------------------------------
--  Utenti
-- -------------------------------------------------
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone         VARCHAR(30)  NULL,
    gender        ENUM('M','F') NULL,
    bio           TEXT        NULL,
    profile_image VARCHAR(255) NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE api_tokens (
    token      CHAR(64) PRIMARY KEY,
    user_id    INT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
--  Titoli (catalogo)
-- -------------------------------------------------
CREATE TABLE titles (
    id              VARCHAR(20) PRIMARY KEY,
    type            ENUM('movie','tvSeries') NOT NULL,
    primary_title   VARCHAR(250) NOT NULL,
    original_title  VARCHAR(250) NOT NULL,
    start_year      YEAR        NULL,
    end_year        YEAR        NULL,
    runtime_seconds INT         NULL,
    plot            TEXT        NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------
--  Immagine del titolo (opzionale)
-- -------------------------------------------------
CREATE TABLE title_images (
    title_id VARCHAR(20) PRIMARY KEY,
    url      VARCHAR(500) NOT NULL,
    width    INT NULL,
    height   INT NULL,
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
--  Generi
-- -------------------------------------------------
CREATE TABLE genres (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- -------------------------------------------------
--  Relazione titolo ↔ genere
-- -------------------------------------------------
CREATE TABLE title_genre (
    title_id VARCHAR(20) NOT NULL,
    genre_id INT NOT NULL,
    PRIMARY KEY (title_id, genre_id),
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
--  Lista personale dell’utente
-- -------------------------------------------------
CREATE TABLE user_titles (
    user_id  INT NOT NULL,
    title_id VARCHAR(20) NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, title_id),
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE
) ENGINE=InnoDB;
