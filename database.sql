-- ============================================================
--  Software License Tracker – database.sql
--  INS3064 Multimedia Design and Web Development
-- ============================================================

CREATE DATABASE IF NOT EXISTS license_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE license_tracker;

-- Drop tables in reverse FK order
DROP TABLE IF EXISTS usage_stats, revocation_logs, expiry_notifications,
                     activation_logs, license_allocations, allocation_rules,
                     license_pools, software_titles, users;

-- 1. Users
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  UNIQUE NOT NULL,
    email         VARCHAR(100) UNIQUE NOT NULL,
    role          VARCHAR(20)  NOT NULL,          -- 'STUDENT' or 'TEACHER'
    department_id VARCHAR(50)
);

-- 2. Software Titles
CREATE TABLE software_titles (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL,
    vendor VARCHAR(100) NOT NULL
);

-- 3. License Pools
CREATE TABLE license_pools (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    software_id        INT      NOT NULL,
    total_quantity     INT      NOT NULL,
    available_quantity INT      NOT NULL,
    expiry_date        DATETIME NOT NULL,
    FOREIGN KEY (software_id) REFERENCES software_titles(id)
);

-- 4. Allocation Rules
CREATE TABLE allocation_rules (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    software_id  INT         NOT NULL,
    target_role  VARCHAR(20) NOT NULL,   -- 'STUDENT' or 'TEACHER'
    duration_days INT        NOT NULL,
    FOREIGN KEY (software_id) REFERENCES software_titles(id)
);

-- 5. License Allocations
CREATE TABLE license_allocations (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    pool_id     INT      NOT NULL,
    software_id INT      NOT NULL,
    user_id     INT      NOT NULL,
    valid_until DATETIME NOT NULL,
    status      VARCHAR(20) DEFAULT 'ACTIVE',     -- 'ACTIVE', 'EXPIRED', 'REVOKED'
    FOREIGN KEY (pool_id)     REFERENCES license_pools(id),
    FOREIGN KEY (software_id) REFERENCES software_titles(id),
    FOREIGN KEY (user_id)     REFERENCES users(id)
);

-- 6. Activation Logs
CREATE TABLE activation_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    allocation_id INT      NOT NULL,
    activated_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (allocation_id) REFERENCES license_allocations(id)
);

-- 7. Expiry Notifications
CREATE TABLE expiry_notifications (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    allocation_id     INT         NOT NULL,
    notification_type VARCHAR(20) NOT NULL,       -- '7_DAYS', '1_DAY'
    sent_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (allocation_id) REFERENCES license_allocations(id)
);

-- 8. Revocation Logs
CREATE TABLE revocation_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    allocation_id INT          NOT NULL,
    reason        VARCHAR(100) NOT NULL,
    revoked_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (allocation_id) REFERENCES license_allocations(id)
);

-- 9. Usage Stats
CREATE TABLE usage_stats (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    software_id      INT         NOT NULL,
    term_name        VARCHAR(50) NOT NULL,         -- e.g. 'HK1_2026'
    total_allocated  INT         DEFAULT 0,
    total_activated  INT         DEFAULT 0,
    activation_rate  DECIMAL(5,2) DEFAULT 0.00,   -- percentage
    FOREIGN KEY (software_id) REFERENCES software_titles(id)
);

-- ============================================================
-- Sample data
-- ============================================================

INSERT INTO software_titles (name, vendor) VALUES
    ('Microsoft Office 365', 'Microsoft'),
    ('MATLAB R2024', 'MathWorks'),
    ('Adobe Creative Cloud', 'Adobe');

INSERT INTO license_pools (software_id, total_quantity, available_quantity, expiry_date) VALUES
    (1, 100, 100, '2027-06-30 23:59:59'),
    (2,  50,  50, '2027-01-15 23:59:59'),
    (3,  30,  30, '2026-12-31 23:59:59');
