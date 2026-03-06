-- 1. Dining Venues Table
CREATE TABLE IF NOT EXISTS dining_venues (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Banquets Table
CREATE TABLE IF NOT EXISTS banquets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    guest_type VARCHAR(100) NOT NULL, -- e.g., VVIP, VIP, Internal
    venue_id BIGINT UNSIGNED NOT NULL,
    description TEXT NULL,
    scheduled_at TIMESTAMP NULL,
    estimated_guests INT NULL,
    status ENUM('DRAFT', 'PENDING_APPROVAL', 'PUBLISHED', 'COMPLETED', 'REJECTED') NOT NULL DEFAULT 'DRAFT',
    
    -- Approval fields
    approved_by VARCHAR(36) NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    created_by VARCHAR(36) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_banquets_venue FOREIGN KEY (venue_id) REFERENCES dining_venues(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Indexes
CREATE INDEX idx_banquets_status_scheduled ON banquets(status, scheduled_at);
CREATE INDEX idx_banquets_venue_id ON banquets(venue_id);
CREATE INDEX idx_banquets_created_by ON banquets(created_by);