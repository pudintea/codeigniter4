Oke 👍 — kamu ingin **menambahkan fitur `where` yang opsional dan fleksibel** di model `Datatables` kamu, supaya bisa diset dari controller. Jadi kalau `where` diset, dia aktif, tapi kalau tidak, tidak mengubah query apa pun.

Berikut cara paling rapi dan fleksibel untuk melakukannya:

---

### 🔧 Langkah 1 — Tambah properti dan method `setWhere()` di model

Kita tambahkan variabel `$where` dan method untuk menyetelnya di model `Datatables`.

```php
<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Datatables extends Model
{
    protected $table;
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];
    protected $where = []; // <-- Tambahkan ini
    protected $request;
    protected $db;
    protected $dt;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    /**
     * Set konfigurasi datatables secara dinamis
     */
    public function setConfig($table, $column_order = [], $column_search = [], $order = [])
    {
        $this->table = $table;
        $this->column_order = $column_order;
        $this->column_search = $column_search;
        $this->order = $order;

        $this->dt = $this->db->table($this->table);

        return $this;
    }

    /**
     * Set kondisi where (opsional)
     */
    public function setWhere($where = [])
    {
        if (!empty($where)) {
            $this->where = $where;
        }
        return $this; // biar bisa chaining
    }

    private function _get_datatables_query()
    {
        $searchValue = $this->request->getPost('search')['value'] ?? null;
        $orderData = $this->request->getPost('order')[0] ?? null;

        // Terapkan where jika ada
        if (!empty($this->where)) {
            $this->dt->where($this->where);
        }

        // Pencarian
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

        // Urutan
        if ($orderData) {
            $colIndex = $orderData['column'];
            $dir = $orderData['dir'];
            if (isset($this->column_order[$colIndex])) {
                $this->dt->orderBy($this->column_order[$colIndex], $dir);
            }
        } else if (!empty($this->order)) {
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
        $builder = $this->db->table($this->table);
        if (!empty($this->where)) {
            $builder->where($this->where);
        }
        return $builder->countAllResults();
    }
}
```

---

### 🚀 Langkah 2 — Gunakan di Controller

Sekarang kamu bisa atur `where` dari controller secara opsional. Misalnya:

```php
public function data_json()
{
    $request = \Config\Services::request();

    // Konfigurasi datatables
    $table          = 'users';
    $column_order   = ['first_name', 'email', 'role', 'active'];
    $column_search  = ['first_name', 'email'];
    $order          = ['first_name' => 'asc'];

    $datamodel = new \App\Models\Datatables($request);
    $datamodel->setConfig($table, $column_order, $column_search, $order);

    // Contoh where (opsional)
    $where = [];
    if ($request->getGet('active') == '1') {
        $where['active'] = 1;
    }
    if ($request->getGet('role')) {
        $where['role'] = $request->getGet('role');
    }

    $datamodel->setWhere($where);

    if ($request->getMethod(true) === 'POST') {
        $lists = $datamodel->get_datatables();
        $data = [];
        $no = $request->getPost('start');

        foreach ($lists as $rowdata) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $rowdata->first_name;
            $row[] = $rowdata->email;
            $row[] = $rowdata->role;
            $row[] = $rowdata->active;
            $row[] = '
                <a href="/users/edit/'.$rowdata->id.'" class="btn btn-sm btn-success shadow-sm" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <form action="/users/hapus/'.$rowdata->id.'" method="post" class="d-inline">
                    <input type="hidden" name="_method" value="DELETE">
                    '.csrf_field().'
                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus"
                        onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            ';
            $data[] = $row;
        }

        $output = [
            'draw' => $request->getPost('draw'),
            'recordsTotal' => $datamodel->count_all(),
            'recordsFiltered' => $datamodel->count_filtered(),
            'data' => $data
        ];

        return $this->response->setJSON($output);
    }
}
```

---

### ✅ Keuntungan Pendekatan Ini

* **Fleksibel:** kamu bisa isi `$where` atau tidak sama sekali.
* **Chaining-friendly:** bisa dipakai seperti `$datamodel->setConfig(...)->setWhere(...)->get_datatables();`
* **Konsisten:** `where` diterapkan ke semua fungsi (`get_datatables`, `count_filtered`, dan `count_all`).

---

Apakah kamu ingin `where`-nya juga bisa menerima bentuk **closure** (misal untuk query yang kompleks seperti `groupStart()` / `orWhere()`)? Kalau iya, saya bisa ubah jadi lebih fleksibel lagi.
