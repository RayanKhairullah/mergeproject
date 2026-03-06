# 🚗 Sistem Manajemen Kendaraan Pelindo

Sistem manajemen kendaraan operasional yang lengkap dengan fitur peminjaman, inspeksi, dan pencatatan pengeluaran.

## 📋 Fitur Utama

### 1. Monitor Kendaraan
- Melihat semua kendaraan yang tersedia
- Status real-time (Available, In Use, Maintenance)
- Informasi kilometer terkini
- Detail peminjam aktif
- Filter dan pencarian

### 2. Peminjaman Kendaraan (User)
**Workflow:**
1. User mengakses Monitor Kendaraan
2. Pilih kendaraan yang tersedia
3. Isi form peminjaman:
   - Pilih kendaraan
   - Nama peminjam (auto-fill dari user login)
   - Tujuan peminjaman
   - Destinasi
   - Kilometer awal (auto-fill dari posisi terakhir)
4. Submit → Otomatis diarahkan ke form pengembalian

### 3. Pengembalian Kendaraan (User)
**Workflow:**
1. Isi kilometer akhir
2. Upload foto speedometer
3. Submit → Kendaraan kembali tersedia
4. Sistem otomatis update kilometer kendaraan

### 4. Kesiapan Kendaraan / Inspeksi (Admin)
**Workflow:**
1. Admin pilih kendaraan
2. Pilih waktu inspeksi (Pagi/Sore)
3. Isi kondisi:
   - Kondisi Ban (text/notes)
   - Kondisi Body & Fender (text/notes)
   - Kondisi Kaca (text/notes)
4. Upload foto masalah (jika ada, max 3 foto)
5. Input posisi kilometer
6. Upload foto speedometer
7. Catatan tambahan (optional)
8. Submit → Kilometer otomatis tersinkron

### 5. Rupa-rupa / Pengeluaran Kendaraan (User)
**Tipe Kegiatan:**
- BBM
- E-Money
- Parkir
- Cuci Mobil
- Lainnya

**Workflow BBM:**
1. Pilih kendaraan
2. Nama (auto-fill)
3. Sumber dana (Uang Muka/Pribadi/Koperasi)
4. Jenis BBM (Pertalite/Pertamax/Pertadex/Pertamax Turbo/Lainnya)
5. Nominal (Rp)
6. Jumlah liter
7. Upload 3 foto:
   - Bukti pembayaran (wajib)
   - Foto mobil belakang (wajib)
   - Indikator BBM (wajib)
8. Catatan (optional)

**Workflow E-Money/Parkir/Cuci Mobil/Lainnya:**
1. Pilih kendaraan
2. Nama (auto-fill)
3. Sumber dana
4. Nominal (Rp)
5. Upload foto:
   - Bukti pembayaran (wajib kecuali parkir)
   - Foto kegiatan (wajib)
6. Catatan (optional)

## 🗄️ Struktur Database

### Tabel: `vehicles`
- `id`: Primary key
- `license_plate`: Plat nomor (unique)
- `current_mileage`: Kilometer saat ini
- `status`: available/in_use/maintenance
- `last_service_date`: Tanggal service terakhir

### Tabel: `loans`
- `id`: Primary key
- `vehicle_id`: Foreign key ke vehicles
- `user_id`: Foreign key ke users
- `purpose`: Tujuan peminjaman
- `destination`: Destinasi
- `start_mileage`: Kilometer awal
- `end_mileage`: Kilometer akhir
- `speedometer_photo_url`: Foto speedometer
- `loan_date`: Tanggal pinjam
- `return_date`: Tanggal kembali
- `status`: active/returned

### Tabel: `inspections`
- `id`: Primary key
- `vehicle_id`: Foreign key ke vehicles
- `user_id`: Foreign key ke users (admin)
- `inspection_time`: morning/afternoon
- `tire_condition`: Kondisi ban
- `body_condition`: Kondisi body
- `glass_condition`: Kondisi kaca
- `issue_photos`: JSON array foto masalah
- `mileage_check`: Posisi kilometer
- `speedometer_photo_url`: Foto speedometer
- `additional_notes`: Catatan tambahan

### Tabel: `vehicle_expenses`
- `id`: Primary key
- `vehicle_id`: Foreign key ke vehicles
- `user_id`: Foreign key ke users
- `expense_type`: BBM/E-Money/Parkir/Cuci Mobil/Lainnya
- `funding_source`: UANG_MUKA/UANG_PRIBADI/KOPERASI
- `fuel_type`: Jenis BBM (nullable)
- `fuel_liters`: Jumlah liter (nullable)
- `nominal`: Nominal pengeluaran
- `documentation_photos`: JSON array foto dokumentasi
- `notes`: Catatan

## 🔄 Sinkronisasi Kilometer

Sistem otomatis menjaga sinkronisasi kilometer antara:
- Peminjaman kendaraan (start_mileage & end_mileage)
- Inspeksi kendaraan (mileage_check)
- Data kendaraan (current_mileage)

**Logika:**
```
Posisi Kilometer: 13240
↓ Peminjaman
Kilometer Akhir: 13310
↓ Auto Update
Posisi Kilometer: 13310
↓ Inspeksi/Peminjaman Berikutnya
Kilometer Awal: 13310
```

## 🎨 Design System

Menggunakan Tailwind CSS v4.2 dengan:
- P3 Wide Gamut Colors (OKLCH)
- Glassmorphism effects
- Container Queries
- Smooth transitions (duration-750)
- Dark mode support

## 🚀 Setup & Installation

### 1. Jalankan Migrations
```bash
php artisan migrate
```

### 2. Jalankan Seeders
```bash
php artisan db:seed
```

### 3. Buat Storage Link
```bash
php artisan storage:link
```

### 4. Set Permissions (Opsional)
Tambahkan permissions untuk vehicle management di `PermissionSeeder.php`:
```php
'view vehicles',
'manage vehicles',
'inspect vehicles',
```

## 📱 Routes

```php
// Vehicle Management
Route::prefix('vehicles')->as('vehicles.')->group(function () {
    Route::get('/monitor', VehicleMonitor::class)->name('monitor');
    Route::get('/loan/{vehicle?}', LoanForm::class)->name('loan');
    Route::get('/return/{loan}', ReturnForm::class)->name('return');
    Route::get('/inspection', InspectionForm::class)->name('inspection');
    Route::get('/expense', ExpenseForm::class)->name('expense');
});
```

## 🔐 Authorization

- **Monitor & Peminjaman**: Semua authenticated users
- **Inspeksi**: Hanya admin (permission: `access dashboard`)
- **Pengeluaran**: Semua authenticated users

## 📊 Models & Relationships

### Vehicle Model
```php
- loans() : HasMany
- inspections() : HasMany
- expenses() : HasMany
- activeLoan() : HasMany (status = active)
- isAvailable() : bool
- updateMileage(int $mileage) : void
```

### Loan Model
```php
- vehicle() : BelongsTo
- user() : BelongsTo
- isActive() : bool
- returnVehicle(int $endMileage, string $photo) : void
```

### Inspection Model
```php
- vehicle() : BelongsTo
- user() : BelongsTo
- Auto-update vehicle mileage on create
```

### VehicleExpense Model
```php
- vehicle() : BelongsTo
- user() : BelongsTo
- isFuelExpense() : bool
```

## 🎯 Best Practices

1. **Foto Upload**: Maksimal 2MB per foto
2. **Validasi Kilometer**: End mileage harus >= start mileage
3. **Status Kendaraan**: Otomatis update saat peminjaman/pengembalian
4. **Dokumentasi**: Semua transaksi tercatat dengan foto bukti
5. **Audit Trail**: Semua aktivitas tercatat dengan user_id dan timestamp

## 🐛 Troubleshooting

### Foto tidak terupload
- Pastikan storage link sudah dibuat: `php artisan storage:link`
- Cek permission folder `storage/app/public`

### Kilometer tidak sinkron
- Cek event listener di `Inspection::booted()`
- Cek method `Vehicle::updateMileage()`

### Error saat submit form
- Cek validasi di Livewire component
- Cek error log: `storage/logs/laravel.log`

## 📝 TODO / Future Enhancements

- [ ] Dashboard admin untuk monitoring semua aktivitas
- [ ] Laporan bulanan pengeluaran per kendaraan
- [ ] Notifikasi reminder service kendaraan
- [ ] Export data ke Excel/PDF
- [ ] Grafik statistik penggunaan kendaraan
- [ ] QR Code untuk quick access peminjaman
- [ ] Mobile app untuk kemudahan akses

## 👥 Credits

Developed for PT Pelindo (Persero)
Laravel 12 + Livewire 3 + Flux UI + Tailwind CSS v4
