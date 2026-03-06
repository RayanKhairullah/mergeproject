# 🚗 Sistem Manajemen Kendaraan Pelindo

## ✅ Setup Selesai!

Sistem manajemen kendaraan telah berhasil diinstall dan siap digunakan.

## 🎯 Akses Sistem

### Halaman Utama
Buka browser dan akses: **http://127.0.0.1:8000/**

Anda akan melihat 5 menu utama:

1. **Monitor Kendaraan** 🖥️
   - Lihat semua kendaraan yang tersedia
   - Status real-time (Tersedia/Sedang Digunakan/Maintenance)
   - Informasi peminjam aktif
   - Route: `/vehicles/monitor`

2. **Form Peminjaman** 📋
   - Ajukan peminjaman kendaraan
   - Pilih kendaraan, isi tujuan & destinasi
   - Kilometer awal otomatis tercatat
   - Route: `/vehicles/loan`

3. **Form Pengembalian** ↩️
   - Kembalikan kendaraan yang dipinjam
   - Input kilometer akhir
   - Upload foto speedometer
   - Route: `/vehicles/return/{loan}`
   - **Note**: Hanya muncul jika ada peminjaman aktif

4. **Kesiapan Mobil** ✅ (Admin Only)
   - Inspeksi kondisi kendaraan
   - Catat kondisi ban, body, kaca
   - Upload foto masalah (jika ada)
   - Posisi kilometer otomatis sinkron
   - Route: `/vehicles/inspection`

5. **Rupa-rupa** 💰
   - Catat pengeluaran kendaraan
   - BBM, E-Money, Parkir, Cuci Mobil, dll
   - Upload bukti pembayaran & foto
   - Route: `/vehicles/expense`

## 🔐 Login

Untuk mengakses sistem, Anda perlu login terlebih dahulu.

### Default Super Admin
Jika belum ada user, buat super admin dengan:
```bash
php artisan app:create-super-admin
```

## 📊 Data Sample

Sistem sudah terisi dengan 5 kendaraan sample:
- B 1234 ABC (15,000 km)
- B 5678 DEF (22,000 km)
- B 9012 GHI (8,500 km)
- B 3456 JKL (31,000 km)
- B 7890 MNO (12,000 km)

## 🎨 Design System

Halaman menggunakan design system modern dengan:
- ✨ Gradient backgrounds
- 🌙 Dark mode support
- 🎯 Smooth transitions (duration-750)
- 📱 Responsive design
- 🎨 P3 Wide Gamut colors (OKLCH)

## 🔄 Workflow

### User Workflow
1. **Monitor** → Lihat kendaraan tersedia
2. **Pinjam** → Isi form peminjaman
3. **Gunakan** → Kendaraan status berubah "Sedang Digunakan"
4. **Kembalikan** → Isi kilometer akhir & upload foto
5. **Selesai** → Kendaraan kembali tersedia

### Admin Workflow
1. **Inspeksi Pagi/Sore** → Cek kondisi kendaraan
2. **Catat Kondisi** → Ban, Body, Kaca
3. **Upload Foto** → Jika ada masalah
4. **Input Kilometer** → Otomatis sinkron dengan sistem
5. **Submit** → Data tersimpan

## 🔧 Troubleshooting

### Foto tidak bisa diupload
```bash
php artisan storage:link
```

### Error saat akses halaman
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild frontend
npm run build
```

### Database error
```bash
# Re-run migrations
php artisan migrate:fresh --seed
```

## 📱 Browser Support

- ✅ Chrome (Recommended)
- ✅ Firefox
- ✅ Edge
- ✅ Safari

## 🚀 Development

Untuk development mode:
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (Frontend)
npm run dev

# Atau gunakan composer script
composer run dev
```

## 📝 Notes

- Semua foto disimpan di `storage/app/public/`
- Maksimal ukuran foto: 2MB
- Format foto: JPG, PNG, WEBP
- Kilometer otomatis sinkron antara peminjaman, pengembalian, dan inspeksi

## 🎯 Next Steps

1. Login ke sistem
2. Explore menu Monitor Kendaraan
3. Coba fitur peminjaman
4. Test form pengembalian
5. (Admin) Coba fitur inspeksi

---

**Developed for PT Pelindo (Persero)**  
Laravel 12 + Livewire 3 + Flux UI + Tailwind CSS v4
