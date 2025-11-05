Bagus banget pertanyaannya 💪 — memahami metode-metode bawaan **Model di CodeIgniter 4** sangat penting supaya kamu bisa ambil, filter, dan olah data dengan cara yang tepat tanpa nulis query SQL manual.

Berikut ini daftar lengkap **fungsi utama yang bisa kamu gunakan di model CodeIgniter 4**, **selain `findAll()`**, lengkap dengan **penjelasan dan contoh penggunaannya** 👇

---

## 🔹 1. `find($id)`

> Mengambil **satu baris data** berdasarkan **primary key**.

```php
$mapelModel = new MapelModel();
$data = $mapelModel->find(1);
```

✅ **Hasil:**
Array 1 baris, misalnya:

```php
[
  'id_mapel' => 1,
  'nama_mapel' => 'Matematika',
  'kode_mapel' => 'MTK'
]
```

---

## 🔹 2. `first()`

> Mengambil **baris pertama** dari query builder (berguna setelah pakai `where()`, `orderBy()`, dll).

```php
$data = $mapelModel->where('kode_mapel', 'MTK')->first();
```

✅ **Hasil:**
Satu array dari baris pertama yang cocok.

---

## 🔹 3. `where($field, $value)`

> Menambahkan kondisi `WHERE` pada query.

```php
$data = $mapelModel->where('kode_mapel', 'MTK')->findAll();
```

Kamu juga bisa pakai array:

```php
$data = $mapelModel->where([
    'kode_mapel' => 'MTK',
    'aktif' => 1
])->findAll();
```

---

## 🔹 4. `orWhere()`

> Sama seperti `where()`, tapi menggunakan operator `OR`.

```php
$data = $mapelModel
    ->where('aktif', 1)
    ->orWhere('kelas', '10')
    ->findAll();
```

---

## 🔹 5. `like()`, `orLike()`, `notLike()`

> Untuk pencarian teks seperti SQL `LIKE`.

```php
$data = $mapelModel->like('nama_mapel', 'mat')->findAll();
// hasil: nama_mapel mengandung "mat"
```

---

## 🔹 6. `orderBy($field, $direction)`

> Mengurutkan hasil query (`ASC` atau `DESC`).

```php
$data = $mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
```

---

## 🔹 7. `limit($jumlah, $offset = 0)`

> Membatasi jumlah data yang diambil.

```php
$data = $mapelModel->limit(10)->findAll();
```

---

## 🔹 8. `countAll()`

> Menghitung total seluruh baris dalam tabel.

```php
$total = $mapelModel->countAll();
```

---

## 🔹 9. `countAllResults()`

> Menghitung jumlah hasil dari query builder dengan kondisi tertentu.

```php
$total = $mapelModel->where('aktif', 1)->countAllResults();
```

---

## 🔹 10. `insert($data)`

> Menambahkan data baru ke tabel.

```php
$mapelModel->insert([
    'nama_mapel' => 'Fisika',
    'kode_mapel' => 'FSK'
]);
```

---

## 🔹 11. `update($id, $data)`

> Mengubah data berdasarkan ID (atau kondisi tertentu).

```php
$mapelModel->update(1, [
    'nama_mapel' => 'Matematika Lanjutan'
]);
```

Atau:

```php
$mapelModel->where('kode_mapel', 'MTK')->set('aktif', 0)->update();
```

---

## 🔹 12. `delete($id)`

> Menghapus data berdasarkan ID.

```php
$mapelModel->delete(1);
```

Atau dengan kondisi:

```php
$mapelModel->where('aktif', 0)->delete();
```

---

## 🔹 13. `save($data)`

> **Auto-insert atau update** — kalau ada ID, maka update, kalau tidak ada, insert.

```php
$mapelModel->save([
    'id_mapel' => 1,
    'nama_mapel' => 'Matematika Dasar'
]);
```

> ✅ Kalau `id_mapel` ada → update
> ❌ Kalau tidak ada → insert baru

---

## 🔹 14. `findColumn($column)`

> Mengambil satu kolom saja dalam bentuk array.

```php
$namaMapel = $mapelModel->findColumn('nama_mapel');
```

✅ **Hasil:**

```php
['Matematika', 'Fisika', 'Biologi']
```

---

## 🔹 15. `builder()`

> Mengambil instance **Query Builder**, kalau kamu ingin query lebih fleksibel (gabungan, join, dsb).

```php
$builder = $mapelModel->builder();
$data = $builder->select('mapel.*, guru.nama_guru')
                ->join('guru', 'guru.id_guru = mapel.id_guru')
                ->get()
                ->getResultArray();
```

---

## 🔹 16. `asArray()` / `asObject()`

> Menentukan bentuk hasil data (array atau object).

```php
$data = $mapelModel->asArray()->findAll();   // hasil: array
$data = $mapelModel->asObject()->findAll();  // hasil: object
```

---

## 🔹 17. `paginate($perPage)`

> Menampilkan data dengan pagination (halaman).

```php
$data['mapel'] = $mapelModel->paginate(10);
$data['pager'] = $mapelModel->pager;
```

Lalu di view:

```php
<?= $pager->links(); ?>
```

---

### 💡 Rangkuman Singkat

| Kategori           | Fungsi                                                   | Keterangan Singkat |
| ------------------ | -------------------------------------------------------- | ------------------ |
| 🔍 Ambil data      | `find()`, `findAll()`, `first()`, `findColumn()`         | Mengambil data     |
| 🔎 Filter data     | `where()`, `orWhere()`, `like()`, `orderBy()`, `limit()` | Menyaring hasil    |
| 📊 Hitung data     | `countAll()`, `countAllResults()`                        | Hitung total baris |
| ✏️ Manipulasi data | `insert()`, `update()`, `delete()`, `save()`             | CRUD               |
| ⚙️ Lain-lain       | `asArray()`, `asObject()`, `builder()`, `paginate()`     | Pengaturan hasil   |

---

Apakah kamu mau saya buatkan **contoh penggunaan kombinasi beberapa fungsi** (misalnya: `where()`, `like()`, `orderBy()` dalam satu query) biar lebih jelas implementasinya di controller?
