# Troubleshooting - Export Feature

## Common Issues & Solutions

### 1. TypeError: number_format() expects float, string given

**Error:**
```
TypeError - number_format(): Argument #1 ($num) must be of type float, string given
```

**Cause:**
Laravel's `decimal` cast returns values as strings to maintain precision. When using `number_format()`, PHP expects a numeric type.

**Solution:**
Cast the value to float before formatting:
```php
// ❌ Wrong
number_format($banquet->cost, 0, ',', '.')

// ✅ Correct
number_format((float) $banquet->cost, 0, ',', '.')
```

**Files affected:**
- `app/Exports/BanquetsExport.php`
- `resources/views/exports/banquets-pdf.blade.php`

---

### 2. strip_tags() expects string, null given

**Error:**
```
TypeError - strip_tags(): Argument #1 ($string) must be of type string, null given
```

**Cause:**
Rich text fields (notes, description) can be null in the database.

**Solution:**
Use null coalescing operator before strip_tags:
```php
// ❌ Wrong
strip_tags($meeting->notes)

// ✅ Correct
$meeting->notes ?? '-'
// or
strip_tags($meeting->notes ?? '')
```

**Files affected:**
- `app/Exports/MeetingsExport.php`
- `app/Exports/BanquetsExport.php`

---

### 3. Memory Limit Exceeded

**Error:**
```
Fatal error: Allowed memory size exhausted
```

**Cause:**
Exporting large datasets (>10,000 records) can consume significant memory.

**Solutions:**

1. **Chunk the data:**
```php
private function getFilteredMeetings()
{
    return Meeting::query()
        ->with(['room', 'creator', 'approver'])
        ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->orderBy('started_at', 'desc')
        ->cursor(); // Use cursor instead of get()
}
```

2. **Increase memory limit temporarily:**
```php
public function exportExcel()
{
    ini_set('memory_limit', '512M');
    // ... export code
}
```

3. **Use queue for large exports:**
```php
public function exportExcel()
{
    dispatch(new ExportMeetingsJob($this->getFilters()));
    session()->flash('success', 'Export sedang diproses...');
}
```

---

### 4. PDF Layout Issues

**Problem:**
Table columns overflow or text is cut off in PDF.

**Solutions:**

1. **Adjust font size:**
```css
/* In PDF template */
body { font-size: 9px; } /* Reduce from 10px */
th, td { font-size: 8px; }
```

2. **Use landscape orientation:**
```php
$pdf->setPaper('a4', 'landscape'); // Already implemented
```

3. **Limit text length:**
```blade
{{ Str::limit(strip_tags($meeting->notes), 80) }}
```

---

### 5. Special Characters Not Displaying

**Problem:**
Indonesian characters (é, ñ, etc.) show as � or boxes in PDF.

**Solution:**
Use DejaVu Sans font (already implemented):
```css
body { 
    font-family: DejaVu Sans, sans-serif; 
}
```

---

### 6. Export Button Not Working

**Symptoms:**
- Button click does nothing
- No download starts
- Console shows Livewire errors

**Checklist:**

1. **Check permissions:**
```php
// User must have view permission
auth()->user()->can('view meetings')
```

2. **Verify Livewire method exists:**
```php
// In component
public function exportExcel() { ... }
public function exportPdf() { ... }
```

3. **Check browser console:**
```javascript
// Look for JavaScript errors
// Check Network tab for failed requests
```

4. **Clear Livewire cache:**
```bash
php artisan livewire:discover
php artisan optimize:clear
```

---

### 7. Slow Export Performance

**Problem:**
Export takes too long (>30 seconds).

**Optimization tips:**

1. **Eager load relationships:**
```php
Meeting::with(['room', 'creator', 'approver'])->get()
```

2. **Select only needed columns:**
```php
Meeting::select(['id', 'title', 'room_id', 'started_at', ...])->get()
```

3. **Add database indexes:**
```php
// In migration
$table->index('status');
$table->index('started_at');
```

4. **Use pagination for preview:**
```php
// Show first 100 records in preview
$meetings->take(100)
```

---

### 8. File Download Not Starting

**Problem:**
Export completes but file doesn't download.

**Solutions:**

1. **Check response headers:**
```php
return response()->streamDownload(function () use ($pdf) {
    echo $pdf->output();
}, 'filename.pdf', [
    'Content-Type' => 'application/pdf',
]);
```

2. **Verify file permissions:**
```bash
# Storage directory must be writable
chmod -R 775 storage/
```

3. **Check browser download settings:**
- Disable popup blocker
- Check download folder permissions
- Try different browser

---

## Testing Export Functionality

### Manual Testing Checklist

- [ ] Export with no filters
- [ ] Export with search filter
- [ ] Export with status filter
- [ ] Export with date filter
- [ ] Export with multiple filters
- [ ] Export empty result set
- [ ] Export large dataset (>1000 records)
- [ ] Test both Excel and PDF formats
- [ ] Verify file naming (includes timestamp)
- [ ] Check data accuracy in exported file
- [ ] Test on different browsers
- [ ] Test with different user roles

### Automated Testing

Run export tests:
```bash
php artisan test --filter=ExportTest
```

Run specific test:
```bash
php artisan test --filter="admin can export meetings to excel"
```

---

## Performance Benchmarks

Expected export times (approximate):

| Records | Excel | PDF |
|---------|-------|-----|
| 100     | <1s   | <2s |
| 1,000   | 2-3s  | 5-7s |
| 5,000   | 8-10s | 20-25s |
| 10,000+ | Use queue | Use queue |

---

## Support & Debugging

### Enable Debug Mode

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

### Livewire Debug

```blade
<!-- Add to view for debugging -->
@livewire('admin.meetings.index', [], key('debug'))
```

### Database Query Logging

```php
// In component
DB::enableQueryLog();
$meetings = $this->getFilteredMeetings();
dd(DB::getQueryLog());
```

---

## Contact

For additional support, check:
- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com
- Maatwebsite Excel: https://docs.laravel-excel.com
- DomPDF: https://github.com/barryvdh/laravel-dompdf
