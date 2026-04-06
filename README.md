# NuSOAP (Client-Server) - Penjumlahan/Pengurangan

Folder ini (`C:\xampp\htdocs\XML\nusoap-debug`) adalah **path utama** yang akan kamu kumpulkan.

Isi folder ini adalah contoh sederhana SOAP Web Service (NuSOAP) dengan:
- `jumlahkan(a, b)` untuk penjumlahan

---

## Cara Menjalankan (Jika hanya pakai folder ini)

Pastikan Apache XAMPP jalan, lalu akses:
- Client: `http://localhost/xml/nusoap-debug/client.php`
- Server (Web description): `http://localhost/xml/nusoap-debug/server.php`
- WSDL: `http://localhost/xml/nusoap-debug/server.php?wsdl`
- Debug: `http://localhost/xml/nusoap-debug/_debug_client.php`

> Kalau kamu ingin URL pendek `http://localhost/xml/client.php` dan `server.php`, itu butuh file wrapper di root project (di luar folder ini).

---

## Snapshot “Sebelum” (Kode Bug)

Aku simpan versi awal yang bug di folder:
- `nusoap-debug/before/client.php`
- `nusoap-debug/before/server.php`

Ini supaya kamu bisa menunjukkan “sebelum vs sesudah” dengan jelas.

---

## Yang Dibenerin (2 client + 1 server)

### 1) Client: short tag `<?` (bisa bikin file tidak dieksekusi)
- Sebelum: `nusoap-debug/before/client.php:1`
- Sesudah: `nusoap-debug/client.php:1`
- Alasan: di sebagian konfigurasi PHP, `short_open_tag` bisa OFF. Aman selalu gunakan `<?php`.

### 2) Client: tidak ada error handling (hasil jadi kosong)
- Sebelum: `nusoap-debug/before/client.php:10` dan `nusoap-debug/before/client.php:12`
- Sesudah: `nusoap-debug/client.php:13` dan `nusoap-debug/client.php:20`
- Alasan: kalau SOAP gagal, `$result` bisa `false`/kosong. Dengan cek `$client->fault` + `$client->getError()`, penyebabnya terlihat.

### 3) Server: belum publish WSDL + nama operasi tidak cocok
- Sebelum: `nusoap-debug/before/server.php:6` dan `nusoap-debug/before/server.php:8`
- Sesudah: `nusoap-debug/server.php:7` dan `nusoap-debug/server.php:9` dan `nusoap-debug/server.php:31`
- Alasan: client memanggil `jumlahkan` tapi server awalnya register `jumlah`. Selain itu server belum `configureWSDL()`, jadi WSDL tidak tersedia. Solusi: `configureWSDL()` + `register("jumlahkan", ...)` + `function jumlahkan(...)`.

---

## Hasil Akhir

Jika service berjalan normal, membuka client akan menampilkan:
- `Hasil penjumlahan dari 10 dan 15 adalah 25`
