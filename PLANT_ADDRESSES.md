# Plant Addresses Management (AMP & CBP)

## Overview
Setiap perusahaan dapat memiliki multiple alamat untuk:
- **AMP** (Asphalt Mixing Plant)
- **CBP** (Concrete Batching Plant)

Karena setiap perusahaan bisa memiliki lebih dari satu lokasi untuk masing-masing jenis pabrik, data disimpan di tabel terpisah `company_plants` dengan struktur one-to-many.

## Changes Made

### Database
1. **Tabel Baru**: `company_plants`
   - `id` (primary key)
   - `company_id` (foreign key)
   - `type` (enum: AMP | CBP)
   - `address` (varchar 500)
   - `timestamps`

2. **Migration untuk cleanup**:
   - Menghapus kolom `asphalt_mixing_plant_address` dan `concrete_batching_plant_address` dari tabel `companies`

### Models
1. **CompanyPlant** (Model baru)
   - `belongs_to` Company
   - Fillable: `company_id`, `type`, `address`

2. **Company** (Updated)
   - Tambahan relation: `plants()`, `ampAddresses()`, `cbpAddresses()`
   - Hapus field dari `$fillable`

### Controllers
1. **CompanyPlantController** (Baru)
   - `index()` - List semua plant addresses
   - `create()` - Form tambah
   - `store()` - Simpan plant baru
   - `edit()` - Form edit
   - `update()` - Update plant
   - `destroy()` - Hapus plant

### Routes
Routes dikelompokkan di middleware `web,auth,user.active`:
```
GET    /company/plants              - List plants
GET    /company/plants/create       - Form create
POST   /company/plants              - Store
GET    /company/plants/{plant}/edit - Form edit
PUT    /company/plants/{plant}      - Update
DELETE /company/plants/{plant}      - Delete
```

### Views
1. **resources/views/company/plants/index.blade.php**
   - Table dengan list plant addresses
   - Tombol edit dan hapus per baris
   - Link ke halaman create

2. **resources/views/company/plants/create.blade.php**
   - Form untuk tambah plant baru
   - Select type (AMP/CBP)
   - Textarea untuk alamat

3. **resources/views/company/plants/edit.blade.php**
   - Form untuk edit plant existing
   - Pre-filled dengan data existing

### Form Registration Changes
- Hapus field dari `resources/views/auth/register.blade.php`:
  - "Alamat Lokasi Asphalt Mixing Plant (Opsional)"
  - "Alamat Lokasi Concrete Batching Plant (Opsional)"

## Usage

### Untuk Admin
Data plant addresses bisa dilihat di:
- Admin Company Detail Page (akan menampilkan plants dari company)

### Untuk Users
Users dapat mengelola plant addresses setelah login:
1. Login
2. Masuk ke `/company/plants`
3. Klik "Tambah Lokasi"
4. Pilih type (AMP/CBP) dan isi alamat
5. Simpan

## Validasi
- **type**: Required, hanya accept 'AMP' atau 'CBP'
- **address**: Required, max 500 karakter

## Relasi Database
```
Company (1) --- (Many) CompanyPlant
```
- Jika company dihapus, semua plant addresses otomatis dihapus (cascadeOnDelete)
- Setiap plant punya owner company, tidak bisa diakses user lain

## Access Control
- Users hanya bisa manage plants dari company mereka sendiri
- Controller memeriksa `$user->companies()->first()` dan memastikan `$plant->company_id` sesuai
