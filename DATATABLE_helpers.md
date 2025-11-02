Mantap 😎 — ini ide bagus banget.
Kita akan bikin sistem **DataTables helper / trait** yang bisa dipakai **di controller manapun** tanpa harus nulis ulang konfigurasi model setiap kali.

Struktur ini sangat umum di proyek CodeIgniter 4 besar supaya:

* bisa reusable,
* lebih rapih,
* dan mudah dikembangkan (misal nanti nambah relasi join, filter dinamis, dll).

---

## 🧩 STRUKTUR AKHIR

```
app/
├── Helpers/
│   └── datatables_helper.php
├── Models/
│   └── DatatablesModel.php
└── Controllers/
    ├── Users.php
    └── Products.php
```

---

## 1️⃣ FILE: `app/Models/DatatablesModel.php`

Ini model utama yang menangani query DataTables secara *generic*:

```php
<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class DatatablesModel extends Model
{
    protected $request;
    protected $db;
    protected $dt;

    protected $table;
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    public function setConfig($table, $column_order = [], $column_search = [], $order = [])
    {
        $this->table = $table;
        $this->column_order = $column_order;
        $this->column_search = $column_search;
        $this->order = $order;
        $this->dt = $this->db->table($this->table);

        return $this;
    }

    private function _get_datatables_query()
    {
        $searchValue = $this->request->getPost('search')['value'] ?? null;
        $orderData = $this->request->getPost('order')[0] ?? null;

        // 🔍 Pencarian
        if ($searchValue) {
            $this->dt->groupStart();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $this->dt->like($item, $searchValue);
                } else {
                    $this->dt->orLike($item, $searchValue);
                }
            }
            $this->dt->groupEnd();
        }

        // 📋 Urutan kolom
        if ($orderData) {
            $colIndex = $orderData['column'];
            $dir = $orderData['dir'];
            if (isset($this->column_order[$colIndex])) {
                $this->dt->orderBy($this->column_order[$colIndex], $dir);
            }
        } elseif (!empty($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();

        if ($this->request->getPost('length') != -1) {
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        }

        return $this->dt->get()->getResult();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }

    public function count_all()
    {
        return $this->db->table($this->table)->countAllResults();
    }
}
```

---

## 2️⃣ FILE: `app/Helpers/datatables_helper.php`

Kita bikin helper supaya di controller tinggal panggil fungsi sederhana.

```php
<?php

use App\Models\DatatablesModel;
use Config\Services;

/**
 * Helper Datatables
 *
 * Cara pakai:
 * datatables_data([
 *    'table' => 'users',
 *    'columns' => ['first_name', 'email', 'role', 'active'],
 *    'searchable' => ['first_name', 'email'],
 *    'order' => ['first_name' => 'asc'],
 *    'formatRow' => function($no, $row) {
 *        return [
 *            $no,
 *            $row->first_name,
 *            $row->email,
 *            $row->role,
 *            $row->active,
 *            '<a href="/users/edit/'.$row->id.'" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a>'
 *        ];
 *    }
 * ]);
 */

if (!function_exists('datatables_data')) {
    function datatables_data(array $config)
    {
        $request = Services::request();
        $model = new DatatablesModel($request);

        $table = $config['table'];
        $columns = $config['columns'] ?? [];
        $searchable = $config['searchable'] ?? [];
        $order = $config['order'] ?? [];
        $formatRow = $config['formatRow'] ?? null;

        $model->setConfig($table, $columns, $searchable, $order);

        if ($request->getMethod(true) === 'POST') {
            $lists = $model->get_datatables();
            $data = [];
            $no = $request->getPost('start');

            foreach ($lists as $row) {
                $no++;
                if (is_callable($formatRow)) {
                    $data[] = $formatRow($no, $row);
                } else {
                    // fallback: tampilkan semua kolom
                    $data[] = array_merge([$no], array_values((array) $row));
                }
            }

            return [
                'draw' => $request->getPost('draw'),
                'recordsTotal' => $model->count_all(),
                'recordsFiltered' => $model->count_filtered(),
                'data' => $data
            ];
        }

        return [];
    }
}
```

---

## 3️⃣ CONTOH PEMAKAIAN DI CONTROLLER (`Users.php`)

```php
<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function data_json()
    {
        helper('datatables');

        $output = datatables_data([
            'table' => 'users',
            'columns' => ['first_name', 'email', 'role', 'active'],
            'searchable' => ['first_name', 'email'],
            'order' => ['first_name' => 'asc'],
            'formatRow' => function($no, $row) {
                return [
                    $no,
                    $row->first_name,
                    $row->email,
                    $row->role,
                    $row->active,
                    '
                        <a href="/users/edit/'.$row->id.'" class="btn btn-sm btn-success shadow-sm" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="/users/hapus/'.$row->id.'" method="post" class="d-inline">
                            <input type="hidden" name="_method" value="DELETE">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus"
                                onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    '
                ];
            }
        ]);

        return $this->response->setJSON($output);
    }
}
```

---

## ⚡️ HASILNYA:

Sekarang kamu cukup panggil:

```php
helper('datatables');
return $this->response->setJSON(
    datatables_data([...])
);
```

Untuk tabel apa pun — tinggal ubah parameter konfigurasinya, **tanpa ubah model atau query** lagi 🎯

---

Kalau kamu mau, saya bisa bantu tambahkan versi **dengan relasi JOIN otomatis** juga (misal ambil data `users` + `roles` dalam satu datatables).
Apakah kamu mau sekalian dibuatkan versi itu juga?
