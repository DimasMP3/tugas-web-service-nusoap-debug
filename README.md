# XML NuSOAP (Client-Server) - Penjumlahan (SOAP)

Project ini adalah contoh sederhana SOAP Web Service menggunakan **NuSOAP** di PHP (umumnya dipakai di XAMPP/Apache).

Folder yang akan kamu kumpulkan (path utama):
- `C:\xampp\htdocs\XML\nusoap-debug\`

> Jika kamu hanya mengumpulkan `nusoap-debug/`, akses URL-nya akan lewat `/xml/nusoap-debug/...`.

---

## Struktur & URL

### 1) Implementasi utama (yang dikumpulkan)
Semua yang penting ada di `nusoap-debug/`:
- `nusoap-debug/client.php` -> SOAP client
- `nusoap-debug/server.php` -> SOAP server + WSDL
- `nusoap-debug/_debug_client.php` -> debug request/response SOAP
- `nusoap-debug/before/` -> snapshot kode sebelum bug (untuk laporan)

URL (kalau hanya pakai `nusoap-debug/`):
- Client: `http://localhost/xml/nusoap-debug/client.php`
- Server (web description): `http://localhost/xml/nusoap-debug/server.php`
- WSDL: `http://localhost/xml/nusoap-debug/server.php?wsdl`
- Debug: `http://localhost/xml/nusoap-debug/_debug_client.php`

### 2) Wrapper URL pendek (opsional, di luar yang dikumpulkan)
Di root `C:\xampp\htdocs\XML\` ada wrapper:
- `client.php:3` -> me-require `nusoap-debug/client.php`
- `server.php:3` -> me-require `nusoap-debug/server.php`
- `_debug_client.php:3` -> me-require `nusoap-debug/_debug_client.php`

URL (kalau wrapper dipakai):
- `http://localhost/xml/client.php`
- `http://localhost/xml/server.php?wsdl`

---

## Gejala Bug Awal

Output client awalnya:

`Hasil penjumlahan dari 10 dan 15 adalah`

Tanpa hasil angka di belakangnya.

---

## Tabel Sebelum vs Sesudah (File + Line)

Snapshot "sebelum" disimpan di:
- `nusoap-debug/before/client.php`
- `nusoap-debug/before/server.php`

| No | Kasus | Sebelum (bug) | Sesudah (fix) | Penjelasan & alasan |
|---:|---|---|---|---|
| 1 | Client pakai short tag `<?` | `nusoap-debug/before/client.php:1` | `nusoap-debug/client.php:1` | `<?` bisa nonaktif (setting `short_open_tag`), sehingga file tidak diproses benar. Aman selalu pakai `<?php`. |
| 2 | Client tidak cek error/fault | `nusoap-debug/before/client.php:10` dan `nusoap-debug/before/client.php:12` | `nusoap-debug/client.php:12` dan `nusoap-debug/client.php:18` | Tanpa cek `$client->fault`/`$client->getError()`, saat SOAP gagal hasil bisa `false`/kosong dan tetap di-echo. Setelah fix, error terlihat jelas. |
| 3 | Server belum publish WSDL + nama operasi tidak cocok | `nusoap-debug/before/server.php:6` dan `nusoap-debug/before/server.php:8` | `nusoap-debug/server.php:7` dan `nusoap-debug/server.php:9` dan `nusoap-debug/server.php:20` | Client memanggil `jumlahkan` tapi server awalnya register `jumlah`. Ditambah server belum `configureWSDL()`, jadi WSDL/web description tidak terbentuk. Fix: `configureWSDL()` + register `jumlahkan` + fungsi `jumlahkan()`. |

---

## Kode Sebelum (Bug) vs Sesudah (Fix)

### 1) Client - short tag

Sebelum (bug) -> `nusoap-debug/before/client.php:1`
```php
<?
```

Sesudah (fix) -> `nusoap-debug/client.php:1`
```php
<?php
```

### 2) Client - error handling

Sebelum (bug) -> `nusoap-debug/before/client.php:10` dan `nusoap-debug/before/client.php:12`
```php
$result = $client->call('jumlahkan', array("a" => $a, "b" => $b));
echo "Hasil penjumlahand dari " . $a . " dan " . $b . " adalah " . $result;
```

Sesudah (fix) -> `nusoap-debug/client.php:12` dan `nusoap-debug/client.php:18`
```php
if ($client->fault) { /* ... */ }
$err = $client->getError();
if ($err) { /* ... */ }
```

### 3) Server - WSDL + register method yang benar

Sebelum (bug) -> `nusoap-debug/before/server.php:6` dan `nusoap-debug/before/server.php:8`
```php
$server->register('jumlah');
function jumlah($a, $b) { return $a + $b; }
```

Sesudah (fix) -> `nusoap-debug/server.php:7` dan `nusoap-debug/server.php:9` dan `nusoap-debug/server.php:20`
```php
$server->configureWSDL("KalkulatorService", $namespace);
$server->register("jumlahkan", /* ... */);
function jumlahkan($a, $b) { return $a + $b; }
```

---

## Hasil Akhir

Jika server berjalan normal:
- Membuka `http://localhost/xml/nusoap-debug/client.php` menampilkan `Hasil penjumlahan dari 10 dan 15 adalah 25`.
- Membuka `http://localhost/xml/nusoap-debug/server.php?wsdl` mengembalikan XML WSDL.
