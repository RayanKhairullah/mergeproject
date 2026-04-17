-- 1. Tabel Karyawan (Peminjam)
CREATE TABLE employees (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Kendaraan
CREATE TABLE vehicles (
    id SERIAL PRIMARY KEY,
    license_plate VARCHAR(15) UNIQUE NOT NULL,
    image VARCHAR(255),
    current_mileage INT NOT NULL DEFAULT 0,
    status ENUM('available', 'in_use', 'maintenance') DEFAULT 'available',
    last_service_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel Peminjaman (Workflow User)
CREATE TABLE loans (
    id SERIAL PRIMARY KEY,
    vehicle_id INT REFERENCES vehicles(id),
    employee_id INT REFERENCES employees(id),
    purpose TEXT NOT NULL,
    destination TEXT NOT NULL,
    start_mileage INT NOT NULL,
    end_mileage INT,
    speedometer_photo_url VARCHAR(255),
    loan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date TIMESTAMP,
    status ENUM('active', 'returned') DEFAULT 'active',
    
    -- Validasi: Kilometer akhir tidak boleh lebih kecil dari awal
    CONSTRAINT check_mileage_logic CHECK (end_mileage >= start_mileage)
);

-- 4. Tabel Inspeksi (Workflow Admin/Kesiapan)
CREATE TABLE inspections (
    id SERIAL PRIMARY KEY,
    vehicle_id INT REFERENCES vehicles(id),
    admin_id INT REFERENCES employees(id), -- Admin juga bagian dari employee
    inspection_time ENUM('morning', 'afternoon') NOT NULL,
    tire_condition TEXT DEFAULT 'Good',
    body_condition TEXT DEFAULT 'Good',
    glass_condition TEXT DEFAULT 'Good',
    issue_photos JSON, -- Menyimpan array URL foto (maks 3)
    mileage_check INT NOT NULL,
    speedometer_photo_url VARCHAR(255) NOT NULL,
    additional_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk BBM, E-Money, Parkir, Cuci Mobil, dll.
CREATE TABLE vehicle_expenses (
    id SERIAL PRIMARY KEY,
    vehicle_id INT REFERENCES vehicles(id),
    employee_id INT REFERENCES employees(id),
    reporter_name VARCHAR(255) NOT NULL,
    
    -- Tipe Kegiatan
    expense_type ENUM('BBM', 'E-Money', 'Parkir', 'Cuci Mobil', 'Lainnya') NOT NULL,
    
    -- Sumber Dana
    funding_source ENUM('UANG_MUKA', 'UANG_PRIBADI', 'KOPERASI_KONSUMEN_SUKA_BAHARI') NOT NULL,
    
    -- Field Spesifik BBM (Hanya diisi jika expense_type = 'BBM')
    fuel_type ENUM('PERTALITE', 'PERTAMAX', 'PERTADEX', 'PERTAMAX TURBO', 'Lainnya'),
    fuel_liters DECIMAL(10, 2), -- Jumlah liter (misal: 15.50)
    
    -- Field Umum
    nominal DECIMAL(12, 2) NOT NULL, -- Rp.
    
    -- Dokumentasi Foto (Disimpan dalam JSON untuk fleksibilitas jumlah foto)
    -- Flow BBM: [Bukti, Foto Belakang Mobil, Indikator BBM]
    -- Flow Lain: [Bukti, Foto Kegiatan]
    documentation_photos JSON NOT NULL, 
    
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

---
--- INDEXING UNTUK PERFORMA ---
---

-- Indexing pada plat nomor dan nama karyawan untuk pencarian cepat di monitor/dashboard
CREATE INDEX idx_vehicle_plate ON vehicles(license_plate);
CREATE INDEX idx_employee_name ON employees(full_name);

-- Indexing pada status untuk filter cepat (misal: mencari mobil yang 'available')
CREATE INDEX idx_vehicle_status ON vehicles(status);

-- Indexing pada Foreign Keys untuk mempercepat proses JOIN tabel
CREATE INDEX idx_loan_vehicle ON loans(vehicle_id);
CREATE INDEX idx_loan_employee ON loans(employee_id);
CREATE INDEX idx_inspection_vehicle ON inspections(vehicle_id);

-- Indexing pada tanggal untuk laporan bulanan/mingguan
CREATE INDEX idx_loan_date ON loans(loan_date);

-- Indexing untuk laporan keuangan
CREATE INDEX idx_expense_vehicle ON vehicle_expenses(vehicle_id);
CREATE INDEX idx_expense_type ON vehicle_expenses(expense_type);
CREATE INDEX idx_expense_date ON vehicle_expenses(created_at);
