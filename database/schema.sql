CREATE TABLE IF NOT EXISTS pastes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    short_code VARCHAR(6) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    password_hash VARCHAR(255) NULL,
    burn_after_reading TINYINT(1) NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY pastes_uuid_unique (uuid),
    UNIQUE KEY pastes_short_code_unique (short_code),
    KEY pastes_expires_at_index (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attachments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    paste_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(127) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    storage_path VARCHAR(512) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY attachments_paste_id_index (paste_id),
    CONSTRAINT attachments_paste_id_foreign
        FOREIGN KEY (paste_id) REFERENCES pastes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_counters (
    counter_key VARCHAR(64) NOT NULL,
    counter_value BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (counter_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
