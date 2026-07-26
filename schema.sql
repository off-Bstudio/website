-- Breaker Studio — database schema (MySQL / MariaDB, e.g. InfinityFree)
-- Import this file via phpMyAdmin's "Import" tab.

CREATE TABLE IF NOT EXISTS admins (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(64) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS accounts (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(32) UNIQUE NOT NULL,
    email         VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('player','moderator','admin') NOT NULL DEFAULT 'player',
    status        ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_accounts_status ON accounts(status);
CREATE INDEX idx_accounts_role ON accounts(role);

-- Game cards shown on the public "Games" page
CREATE TABLE IF NOT EXISTS games (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(120) NOT NULL,
    genre_en       VARCHAR(80) NOT NULL,
    genre_fr       VARCHAR(80) NOT NULL,
    year           INT NOT NULL,
    status_key     ENUM('available','development') NOT NULL DEFAULT 'available',
    description_en TEXT NOT NULL,
    description_fr TEXT NOT NULL,
    color          VARCHAR(10) NOT NULL DEFAULT 'g1',
    position       INT NOT NULL DEFAULT 0,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_games_position ON games(position);

-- Job offers shown on the public "Careers" page
CREATE TABLE IF NOT EXISTS job_offers (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title_en       VARCHAR(120) NOT NULL,
    title_fr       VARCHAR(120) NOT NULL,
    department_en  VARCHAR(80) NOT NULL,
    department_fr  VARCHAR(80) NOT NULL,
    location_key   ENUM('remote','hybrid','onsite') NOT NULL DEFAULT 'remote',
    position       INT NOT NULL DEFAULT 0,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_jobs_position ON job_offers(position);

-- Single-row-per-key site settings (e.g. recruiting_open)
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key   VARCHAR(64) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------- seed data

INSERT INTO admins (username, password_hash) VALUES
    -- password: admin123  (change this immediately after first login)
    ('admin', '$2y$10$G3LDojXmhwFuHBUDWfw3AOvuy1axbFhplchhhowAN7kTwo2kZS2ZO');

INSERT INTO games (title, genre_en, genre_fr, year, status_key, description_en, description_fr, color, position) VALUES
('VOID RUNNERS', 'Tactics', 'Tactique', 2024, 'available',
 'A squad tactics roguelike where every failed run permanently rewrites the map for the next one.',
 'Un rogue-like tactique en escouade où chaque tentative ratée réécrit définitivement la carte pour la suivante.',
 'g1', 1),
('IRON ECHO', 'Action', 'Action', 2022, 'available',
 'A melee brawler where damage taken becomes ammunition — the more you''re hit, the harder you hit back.',
 'Un jeu de combat où les dégâts subis se transforment en munitions — plus vous encaissez, plus vous frappez fort.',
 'g2', 2),
('NEON AGE', 'Puzzle', 'Puzzle', 2020, 'available',
 'A city-building puzzle about rerouting light through a collapsing skyline before the grid fails.',
 'Un puzzle de construction urbaine où il faut rediriger la lumière dans une skyline qui s''effondre avant la panne du réseau.',
 'g3', 3),
('GLASS ORBIT', 'Platformer', 'Plateforme', 2027, 'development',
 'A momentum platformer where the level geometry only exists while you''re looking at it.',
 'Un jeu de plateforme basé sur l''élan, où la géométrie du niveau n''existe que tant que vous la regardez.',
 'g4', 4),
('FRACTURE POINT', 'Survival', 'Survie', 2019, 'available',
 'Our debut title — a co-op survival game where the map cracks apart the longer your team stays in one place.',
 'Notre premier titre — un jeu de survie coopératif où la carte se fissure plus votre équipe reste longtemps au même endroit.',
 'g5', 5);

INSERT INTO job_offers (title_en, title_fr, department_en, department_fr, location_key, position) VALUES
('Senior Gameplay Engineer', 'Ingénieur·e gameplay senior', 'Engineering', 'Ingénierie', 'remote', 1),
('Systems Designer', 'Designer de systèmes', 'Design', 'Design', 'remote', 2),
('Technical Artist', 'Artiste technique', 'Art', 'Art', 'hybrid', 3),
('Community Manager', 'Gestionnaire de communauté', 'Marketing', 'Marketing', 'remote', 4);

INSERT INTO site_settings (setting_key, setting_value) VALUES ('recruiting_open', 'true');
