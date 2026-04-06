# XML NuSOAP (Client-Server) - Penjumlahan/Pengurangan

Repo ini berisi contoh sederhana **SOAP Web Service** menggunakan **NuSOAP** di PHP (umumnya dipakai di XAMPP/Apache).

Tujuan akhirnya:
- Membuka `http://localhost/xml/client.php` menampilkan hasil perhitungan (contoh: `10 + 15 = 25`).
- Membuka `http://localhost/xml/server.php?wsdl` menampilkan **WSDL**.
- Ada halaman debug untuk melihat request/response SOAP.

> Catatan path: folder project ini diasumsikan berada di `C:\xampp\htdocs\XML`.  
> Maka URL yang dipakai Apache: `http://localhost/xml/...` (folder `XML` jadi path `/xml`).

---

## Struktur File Penting

Ada dua layer:
1) **Wrapper di root** (biar URL sesuai yang kamu mau: `/xml/client.php`, `/xml/server.php`)
2) **Implementasi sebenarnya** ada di folder `nusoap-debug/`

File root:
- `client.php` -> wrapper yang memanggil `nusoap-debug/client.php`
- `server.php` -> wrapper yang memanggil `nusoap-debug/server.php`
- `_debug_client.php` -> wrapper yang memanggil `nusoap-debug/_debug_client.php`

Implementasi:
- `nusoap-debug/client.php` -> SOAP client (memanggil method service)
- `nusoap-debug/server.php` -> SOAP server (register method + WSDL)
- `nusoap-debug/lib/nusoap.php` -> library NuSOAP (sudah dipatch supaya kompatibel dan WSDL bisa keluar)

---

## Cara Menjalankan

1) Pastikan **Apache XAMPP** jalan.
2) Pastikan folder project ini ada di `htdocs`:
   - `C:\xampp\htdocs\XML\`
3) Buka URL ini:
   - Client: `http://localhost/xml/client.php`
   - Server (Web description): `http://localhost/xml/server.php`
   - WSDL: `http://localhost/xml/server.php?wsdl`
   - Debug: `http://localhost/xml/_debug_client.php`

Jika semuanya benar, `client.php` akan menampilkan hasil perhitungan.

---

## Masalah Awal (Gejala Bug)

Kamu melaporkan output client seperti ini:

`Hasil penjumlahan dari 10 dan 15 adalah`

Tanpa angka di belakangnya (kosong).

Penyebabnya bukan 1 hal saja—umumnya kombinasi:
- Client memanggil method yang tidak ter-register di server (beda nama).
- Client memakai mode **WSDL** tetapi server tidak menyediakan WSDL.
- Tidak ada error handling, jadi saat gagal, tetap `echo` hasil kosong.
- Pada beberapa konfigurasi, `<?` (short tag) membuat script tidak dieksekusi.

---

## Yang Dibenerin (Ringkas)

### A) Perbaikan di Client (2 kasus)

#### 1) Short tag `<?` → diganti `<?php`
Short tag bisa **OFF** di php.ini, akibatnya file dianggap teks biasa / tidak diproses semestinya.

#### 2) Tidak ada error handling
Awalnya client langsung `echo $result`, padahal kalau SOAP error, `$result` bisa kosong/false.
Solusi: cek `$client->fault` dan `$client->getError()` dulu.

### B) Perbaikan di Server (1 kasus)

#### 1) Server belum publish WSDL + nama operasi tidak cocok
Awalnya client memanggil `jumlahkan`, server register `jumlah`.
Selain itu, server belum `configureWSDL()`, jadi saat dibuka via browser muncul:
- “This service does not provide a Web description” (atau WSDL tidak tersedia)

Solusi:
- Tambahkan `configureWSDL(...)`
- Register method `jumlahkan` (dan opsional `kurangkan`)
- Pastikan fungsi PHP yang dieksekusi namanya sama (`function jumlahkan(...)`)

---

## Tabel Sebelum vs Sesudah (Dengan File & Line)

Tabel ini merangkum perbaikan utama beserta **lokasi file dan nomor baris**.
File “sebelum” disimpan sebagai snapshot di folder `before/` supaya bisa dilihat ulang dengan jelas.

| No | Kasus | Sebelum (bug) | Sesudah (fix) | Penjelasan & alasan |
|---:|---|---|---|---|
| 1 | Client pakai short tag `<?` | `before/client.php:1` | `nusoap-debug/client.php:1` | Short tag bisa **nonaktif** di PHP, bikin script tidak diproses benar. Solusi aman: selalu pakai `<?php`. |
| 2 | Client tidak cek error/fault (hasil bisa kosong) | `before/client.php:10` dan `before/client.php:12` | `nusoap-debug/client.php:13` dan `nusoap-debug/client.php:20` | Kalau SOAP gagal, `$result` bisa `false`/kosong. Dengan cek `$client->fault` dan `$client->getError()`, error jadi kelihatan dan proses berhenti dengan pesan yang jelas. |
| 3 | Server belum publish WSDL + nama operasi tidak cocok | `before/server.php:6` dan `before/server.php:8` | `nusoap-debug/server.php:7` dan `nusoap-debug/server.php:9` dan `nusoap-debug/server.php:31` | Client memanggil `jumlahkan`, tapi server awalnya register `jumlah`. Selain itu server belum `configureWSDL()`, sehingga WSDL/web description tidak terbentuk. Solusi: `configureWSDL(...)` + `register("jumlahkan", ...)` + `function jumlahkan(...)`. |

Tambahan (biar URL sesuai instruksi kamu):
- `client.php:3` dan `server.php:3` adalah **wrapper** supaya endpoint tetap bisa diakses di `http://localhost/xml/client.php` dan `http://localhost/xml/server.php` walau implementasi ada di `nusoap-debug/`.

## Kode “Sebelum” vs “Sesudah”

Bagian ini menunjukkan potongan kode yang *bug* dan versi yang sudah dibenerin.

### 1) Client — short tag

**Sebelum (bug)**
```php
<?
require_once "lib/nusoap.php";
// ...
?>
```

**Sesudah (fix)**
```php
<?php
require_once __DIR__ . "/lib/nusoap.php";
// ...
```

### 2) Client — tidak ada error handling

**Sebelum (bug)**
```php
$result = $client->call('jumlahkan', array("a" => $a, "b" => $b));
echo "Hasil penjumlahan dari " . $a . " dan " . $b . " adalah " . $result;
```

**Sesudah (fix) — contoh di `nusoap-debug/client.php`**
```php
$resultTambah = $client->call('jumlahkan', array("a" => $a, "b" => $b));

if ($client->fault) {
    echo "SOAP Fault:\n";
    print_r($resultTambah);
    exit;
}

$err = $client->getError();
if ($err) {
    echo "SOAP Error: " . $err;
    exit;
}

echo "Hasil penjumlahan dari " . $a . " dan " . $b . " adalah " . $resultTambah;
```

### 3) Server — register method + WSDL

**Sebelum (bug)**
```php
$server = new soap_server();
$server->register('jumlah');

function jumlah($a, $b) {
    return $a + $b;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
```

**Sesudah (fix) — contoh di `nusoap-debug/server.php`**
```php
$server = new soap_server();
$namespace = "urn:kalkulator";
$server->configureWSDL("KalkulatorService", $namespace);

$server->register(
    "jumlahkan",
    array("a" => "xsd:int", "b" => "xsd:int"),
    array("return" => "xsd:int"),
    $namespace
);

function jumlahkan($a, $b) {
    return $a + $b;
}

$rawPostData = file_get_contents("php://input");
$server->service($rawPostData);
```

---

## Kenapa Aku Bikin File Wrapper di Root?

Kamu ingin URL seperti ini:
- `http://localhost/xml/client.php`
- `http://localhost/xml/server.php`

Tapi implementasi yang “rapi” aku taruh di `nusoap-debug/`.

Supaya URL tetap pendek (sesuai instruksi kamu), file root hanya **require** file implementasi:
- `client.php` (root) → `require_once __DIR__ . "/nusoap-debug/client.php";`
- `server.php` (root) → `require_once __DIR__ . "/nusoap-debug/server.php";`
- `_debug_client.php` (root) → `require_once __DIR__ . "/nusoap-debug/_debug_client.php";`

Dengan cara ini:
- Kamu tetap akses endpoint di `/xml/...`
- Tapi kode yang “utama” tetap terkumpul rapi di `nusoap-debug/`

---

## Debug SOAP Request/Response

Untuk melihat payload SOAP yang dikirim dan diterima:
1) Buka `http://localhost/xml/_debug_client.php`
2) Lihat output `request` dan `response` yang di-dump.

Kegunaan debug ini:
- memastikan nama method (`jumlahkan`) benar
- memastikan parameter `a` dan `b` terkirim
- memastikan server merespon nilai yang benar

---

## Troubleshooting (Kalau Masih Kosong / Error)

1) **WSDL bisa dibuka?**
   - Coba `http://localhost/xml/server.php?wsdl`
   - Jika tidak keluar XML WSDL, berarti server belum benar publish WSDL atau Apache/PHP error.

2) **Nama operasi sama?**
   - Client call: `jumlahkan`
   - Server register: `jumlahkan`
   - Fungsi PHP: `function jumlahkan(...)`

3) **Short tag**
   - Pastikan file dimulai dengan `<?php` (bukan `<?`).

4) **Cek error handling**
   - Jika client menampilkan `SOAP Error: ...`, itu *lebih bagus* daripada kosong karena kita jadi tahu penyebabnya.

5) **`localhost` vs `127.0.0.1`**
   - Di sebagian setup, NuSOAP/PHP bisa gagal connect ke `localhost`.
   - Solusi cepat: pakai `127.0.0.1` (contoh sudah dipakai di `nusoap-debug/client.php`).

---

## Hasil Akhir yang Diharapkan

Saat membuka:
- `http://localhost/xml/client.php`

Muncul:
- `Hasil penjumlahan dari 10 dan 15 adalah 25`

Dan:
- `http://localhost/xml/server.php?wsdl` mengembalikan XML WSDL (bukan halaman error).
