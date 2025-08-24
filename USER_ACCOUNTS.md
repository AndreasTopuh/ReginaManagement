# Akun User Regina Hotel Management System

## Akun yang Tersedia

### 1. Owner (Pemilik Hotel)
- **Username:** `owner`
- **Password:** `admin123`
- **Role:** Owner
- **Nama Lengkap:** Owner Regina Hotel
- **Akses:** Akses penuh ke seluruh sistem
- **Fungsi:**
  - Melihat semua laporan dan dashboard
  - Mengelola user dan role
  - Mengakses semua fitur manajemen hotel
  - Melihat data finansial dan statistik

### 2. Admin (Administrator)
- **Username:** `admin`
- **Password:** `admin123`
- **Role:** Admin
- **Nama Lengkap:** Admin Hotel
- **Akses:** Akses administratif
- **Fungsi:**
  - Mengelola kamar dan lantai
  - Mengelola tipe kamar dan harga
  - Mengatur sistem hotel
  - Melihat laporan operasional
  - Mengelola data tamu

### 3. Receptionist (Resepsionis)
- **Username:** `receptionist`
- **Password:** `admin123`
- **Role:** Receptionist
- **Nama Lengkap:** Receptionist 1
- **Akses:** Akses front desk
- **Fungsi:**
  - Check-in dan check-out tamu
  - Membuat dan mengelola booking
  - Melihat status kamar
  - Mengelola data tamu
  - Cetak invoice dan laporan booking

## Cara Login

1. Buka halaman login: `http://localhost/reginahotel/login.php`
2. Masukkan username dan password sesuai dengan akun yang diinginkan
3. Klik tombol "Login"

## Catatan Keamanan

- **PENTING:** Segera ganti password default `admin123` dengan password yang lebih aman
- Password disimpan dalam format hash menggunakan `password_hash()` PHP
- Setiap role memiliki akses yang berbeda sesuai dengan tingkat kewenangan

## Role Permissions

### Owner
- ✅ Dashboard lengkap
- ✅ Manajemen user
- ✅ Laporan keuangan
- ✅ Semua fitur admin dan receptionist

### Admin  
- ✅ Manajemen kamar dan lantai
- ✅ Pengaturan sistem
- ✅ Laporan operasional
- ✅ Semua fitur receptionist
- ❌ Manajemen user (terbatas)

### Receptionist
- ✅ Booking dan reservasi
- ✅ Check-in/Check-out
- ✅ Manajemen tamu
- ✅ Laporan booking
- ❌ Pengaturan sistem
- ❌ Manajemen user

## Database Information

- **Database:** `regina_hotel`
- **Table Users:** `users`
- **Table Roles:** `roles`
- **User Database:** `hotel_admin`
- **Password Database:** `passwordku123`

Semua akun sudah siap digunakan dan telah diverifikasi dapat login dengan sukses.
