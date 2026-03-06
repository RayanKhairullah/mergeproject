CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE -- Contoh: 'Sains', 'Teknologi'
) ENGINE=InnoDB;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    description TEXT,
    cover_image VARCHAR(255), -- Path ke file gambar
    file_path VARCHAR(255),    -- Path ke file PDF/Ebook
    category_id INT UNSIGNED,
    download_count INT DEFAULT 0, -- Untuk Bagian 2: Terpopuler
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Untuk Bagian 1: Baru Diunggah
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_book_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_review_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Mempercepat Bagian 1: Buku yang baru diunggah (Sorting by Date)
CREATE INDEX idx_books_created_at ON books(created_at);

-- Mempercepat Bagian 2: Buku paling banyak diunduh (Sorting by Popularity)
CREATE INDEX idx_books_download_count ON books(download_count);

-- Mempercepat Bagian 3: Filter berdasarkan kategori (Foreign Key Optimization)
-- Catatan: MySQL biasanya otomatis membuat index untuk Foreign Key, 
-- namun menambahkannya secara eksplisit memastikan performa join tetap stabil.
CREATE INDEX idx_books_category_id ON books(category_id);

-- Mempercepat Fitur Pencarian Judul
CREATE INDEX idx_books_title ON books(title);

-- Mempercepat pengambilan review berdasarkan buku tertentu
CREATE INDEX idx_reviews_book_id ON reviews(book_id);

-- Mempercepat pengambilan review berdasarkan user (untuk halaman profil/history)
CREATE INDEX idx_reviews_user_id ON reviews(user_id);