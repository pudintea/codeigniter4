Pertanyaan bagus banget 🔥 — kamu benar, metode seperti `select()`, `whereIn()`, dan `whereNotIn()` **juga tersedia di CodeIgniter 4**, tapi memang bukan bawaan khusus dari `Model`, melainkan dari **Query Builder** yang bisa kamu akses langsung lewat instance model (karena `Model` di CI4 mewarisi `CodeIgniter\Database\BaseBuilder`).

Mari kita bahas tiga metode itu + bonus beberapa metode sejenisnya biar lengkap 👇

---

## 🔹 1. `select($fields)`

> Menentukan kolom apa saja yang akan diambil (seperti `SELECT` di SQL).

### ✅ Contoh:

```php
$mapelModel = new \App\Models\MapelModel();

// Ambil hanya kolom nama_mapel dan kode_mapel
$data = $mapelModel->select('nama_mapel, kode_mapel')->findAll();
```

### 🧠 Keterangan:

* Kalau kamu **tidak memanggil `select()`**, maka CI4 otomatis melakukan `SELECT *`.
* Kamu juga bisa menambahkan alias:

  ```php
  $mapelModel->select('nama_mapel AS mapel, kode_mapel')->findAll();
  ```

---

## 🔹 2. `selectMax()`, `selectMin()`, `selectAvg()`, `selectSum()`

> Untuk operasi agregat SQL (MAX, MIN, AVG, SUM).

### ✅ Contoh:

```php
$mapelModel->selectMax('nilai')->findAll();
$mapelModel->selectSum('jumlah_siswa')->findAll();
```

---

## 🔹 3. `whereIn($field, array $values)`

> Menghasilkan klausa `WHERE field IN (...)`.

### ✅ Contoh:

```php
$idList = [1, 2, 3];

$data = $mapelModel->whereIn('id_mapel', $idList)->findAll();
```

### 🔍 Hasil SQL kira-kira:

```sql
SELECT * FROM mapel WHERE id_mapel IN (1, 2, 3);
```

---

## 🔹 4. `whereNotIn($field, array $values)`

> Kebalikan dari `whereIn()` — menghasilkan `WHERE field NOT IN (...)`.

### ✅ Contoh:

```php
$idList = [1, 2, 3];

$data = $mapelModel->whereNotIn('id_mapel', $idList)->findAll();
```

### 🔍 Hasil SQL kira-kira:

```sql
SELECT * FROM mapel WHERE id_mapel NOT IN (1, 2, 3);
```

---

## 🔹 5. `selectDistinct($fields)`

> Mengambil data unik dari kolom tertentu (`SELECT DISTINCT`).

### ✅ Contoh:

```php
$data = $mapelModel->selectDistinct('kelas')->findAll();
```

---

## 🔹 6. `groupBy($field)`

> Mengelompokkan hasil berdasarkan kolom tertentu.

### ✅ Contoh:

```php
$data = $mapelModel->select('kelas, COUNT(*) as total')
                   ->groupBy('kelas')
                   ->findAll();
```

---

## 🔹 7. `having()` dan `orHaving()`

> Untuk filter data setelah `GROUP BY`.

### ✅ Contoh:

```php
$data = $mapelModel->select('kelas, COUNT(*) as total')
                   ->groupBy('kelas')
                   ->having('total >', 30)
                   ->findAll();
```

---

## 🔹 8. Kombinasi Beberapa Query Builder Sekaligus

Kamu bisa menggabungkan `select()`, `whereIn()`, `orderBy()`, dll seperti ini:

```php
$data = $mapelModel
    ->select('id_mapel, nama_mapel, kode_mapel')
    ->where('aktif', 1)
    ->whereIn('kelas', ['10', '11'])
    ->orderBy('nama_mapel', 'ASC')
    ->findAll();
```

---

## 🔹 9. Menggunakan `builder()` secara langsung (opsional)

Kalau kamu butuh query yang sangat kompleks, kamu bisa ambil instance Query Builder-nya langsung:

```php
$builder = $mapelModel->builder();

$data = $builder->select('m.nama_mapel, g.nama_guru')
                ->from('mapel m')
                ->join('guru g', 'g.id_guru = m.id_guru')
                ->whereNotIn('m.id_mapel', [5, 6, 7])
                ->orderBy('m.nama_mapel', 'ASC')
                ->get()
                ->getResultArray();
```

---

## ✨ Ringkasan Tambahan Fungsi Query Builder CI4

| Kategori          | Fungsi                                                                      | Deskripsi                   |
| ----------------- | --------------------------------------------------------------------------- | --------------------------- |
| 🎯 Kolom          | `select()`, `selectMax()`, `selectMin()`, `selectSum()`, `selectDistinct()` | Tentukan kolom yang diambil |
| 🔎 Kondisi        | `where()`, `orWhere()`, `whereIn()`, `whereNotIn()`, `like()`, `notLike()`  | Filter hasil                |
| 🔗 Gabungan       | `join()`, `leftJoin()`, `rightJoin()`                                       | Menggabungkan tabel         |
| 📊 Kelompok       | `groupBy()`, `having()`                                                     | Agregasi data               |
| 🧾 Urutan & Batas | `orderBy()`, `limit()`, `offset()`                                          | Mengatur tampilan data      |
| ⚙️ Eksekusi       | `findAll()`, `first()`, `get()`, `getResultArray()`                         | Jalankan query              |

---

Kalau kamu mau, saya bisa bantu buatin contoh **controller + model + view** yang menampilkan data `mapel` dengan `whereIn()`, `select()`, dan `orderBy()` supaya kamu bisa langsung praktek.
Apakah mau saya buatkan contohnya juga?
