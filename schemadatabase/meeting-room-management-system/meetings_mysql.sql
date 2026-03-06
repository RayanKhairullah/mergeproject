-- 1. Rooms Table
CREATE TABLE IF NOT EXISTS rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Room name/identifier',
    capacity INT NOT NULL DEFAULT 0 COMMENT 'Maximum capacity of the room',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Meeting rooms available for bookings';

-- 2. Meetings Table
CREATE TABLE IF NOT EXISTS meetings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL COMMENT 'Meeting title',
    notes TEXT NULL COMMENT 'Meeting notes (rich text HTML), optional',
    show_notes_on_monitor BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Whether to display notes on meeting monitor TV',
    room_id BIGINT UNSIGNED NOT NULL,
    started_at TIMESTAMP NULL COMMENT 'Meeting start time',
    ended_at TIMESTAMP NULL COMMENT 'Meeting end time (for auto-complete feature)',
    duration INT NOT NULL DEFAULT 60 COMMENT 'Meeting duration in minutes',
    estimated_participants INT NULL DEFAULT 0 COMMENT 'Estimated number of meeting participants',
    status ENUM('DRAFT', 'PENDING_APPROVAL', 'PUBLISHED', 'COMPLETED', 'REJECTED') NOT NULL DEFAULT 'DRAFT' 
        COMMENT 'DRAFT=not visible, PENDING_APPROVAL=waiting for SDM approval, PUBLISHED=visible on monitor, COMPLETED=finished, REJECTED=rejected by SDM',
    
    -- Approval fields (UUIDs converted to VARCHAR(36) for MySQL)
    approved_by VARCHAR(36) NULL COMMENT 'User (SDM) who approved the meeting',
    approved_at TIMESTAMP NULL COMMENT 'Timestamp when meeting was approved',
    rejection_reason TEXT NULL COMMENT 'Reason for rejection if meeting was rejected',
    created_by VARCHAR(36) NULL COMMENT 'User who created the meeting',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Constraints & Foreign Keys
    CONSTRAINT fk_meetings_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    CONSTRAINT check_duration CHECK (duration > 0)
) ENGINE=InnoDB COMMENT='Meeting schedules and bookings';

-- 3. Indexes
CREATE INDEX idx_meetings_status_started ON meetings(status, started_at);
CREATE INDEX idx_meetings_room_id ON meetings(room_id);
CREATE INDEX idx_meetings_created_by ON meetings(created_by);
CREATE INDEX idx_meetings_ended_at ON meetings(ended_at);
CREATE INDEX idx_meetings_approved_by ON meetings(approved_by);