# Fitur Export Laporan Meeting & Banquet

## 📋 Overview

Fitur export laporan memungkinkan admin untuk mengekspor data meeting dan banquet ke dalam format Excel (.xlsx) dan PDF untuk keperluan dokumentasi dan pelaporan.

## ✨ Fitur

### 1. Export Excel
- Format: `.xlsx`
- Library: Maatwebsite Excel
- Fitur:
  - Header kolom yang jelas
  - Data terformat dengan baik
  - Support untuk semua filter yang aktif
  - Nama file dengan timestamp

### 2. Export PDF
- Format: `.pdf`
- Library: DomPDF
- Fitur:
  - Layout landscape untuk tabel yang lebar
  - Header dengan logo/branding
  - Footer dengan informasi pencetak
  - Informasi filter yang diterapkan
  - Status badge dengan warna

## 🎯 Cara Penggunaan

### Meeting Export

1. Buka halaman `/admin/meetings`
2. (Opsional) Terapkan filter:
   - Pencarian berdasarkan judul
   - Filter status (Draft, Pending, Published, dll)
   - Filter ruangan
   - Filter tanggal
3. Klik tombol "Export Excel" atau "Export PDF"
4. File akan otomatis terdownload

### Banquet Export

1. Buka halaman `/admin/banquets`
2. (Opsional) Terapkan filter:
   - Pencarian berdasarkan judul
   - Filter status
   - Filter tempat (venue)
   - Filter tipe tamu
   - Filter tanggal
3. Klik tombol "Export Excel" atau "Export PDF"
4. File akan otomatis terdownload

## 📊 Data yang Diekspor

### Meeting
- ID
- Judul Meeting
- Ruangan
- Tanggal Mulai
- Tanggal Selesai
- Durasi (Menit)
- Estimasi Peserta
- Status
- Pembuat
- Disetujui Oleh
- Tanggal Disetujui
- Catatan

### Banquet
- ID
- Judul Acara
- Tempat
- Tipe Tamu
- Tanggal Acara
- Estimasi Tamu
- Biaya
- Status
- Pembuat
- Disetujui Oleh
- Tanggal Disetujui
- Deskripsi

## 🔧 Technical Details

### File Structure

```
app/
├── Exports/
│   ├── MeetingsExport.php      # Excel export untuk meetings
│   └── BanquetsExport.php      # Excel export untuk banquets
│
resources/views/exports/
├── meetings-pdf.blade.php      # Template PDF untuk meetings
└── banquets-pdf.blade.php      # Template PDF untuk banquets
```

### Export Classes

Kedua export class mengimplementasikan:
- `FromCollection` - Untuk data source
- `WithHeadings` - Untuk header kolom
- `WithMapping` - Untuk format data
- `WithStyles` - Untuk styling (bold header)

### PDF Templates

Template PDF menggunakan:
- DejaVu Sans font (support UTF-8)
- Landscape orientation
- Responsive table layout
- Status badges dengan warna

## 🧪 Testing

Test coverage mencakup:
- Export Excel functionality
- Export PDF functionality
- Data mapping correctness
- Filter integration

Jalankan test:
```bash
php artisan test --filter=ExportTest
```

## 🎨 UI Components

Tombol export menggunakan Flux UI:
- Icon: `arrow-down-tray` (Excel), `document-text` (PDF)
- Variant: `outline`
- Size: `sm`
- Responsive text (hidden pada mobile)

## 📝 Notes

- Export mengikuti filter yang aktif di halaman
- Nama file menggunakan timestamp untuk uniqueness
- HTML tags di notes/description otomatis dibersihkan
- Support untuk localization (ID/EN)
- Permission check: User harus memiliki akses ke halaman meeting/banquet

## 🔐 Permissions

Fitur export tersedia untuk user dengan permission:
- `view meetings` (untuk meeting export)
- `view banquets` (untuk banquet export)

## 🚀 Future Enhancements

Potensi pengembangan:
- [ ] Export ke format CSV
- [ ] Scheduled export (email laporan berkala)
- [ ] Custom column selection
- [ ] Chart/grafik di PDF
- [ ] Batch export multiple modules
