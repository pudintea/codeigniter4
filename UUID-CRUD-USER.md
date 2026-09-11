Tentu. Kita bikin **CRUD User dengan UUID sebagai primary key**, **MySQL**, dan **CodeIgniter 4** dari nol sampai bisa dipakai.

 Saya akan ajarkan dengan pendekatan yang cukup profesional, bukan sekadar “yang penting jalan”. Kita akan menggunakan:

 - CodeIgniter 4
- MySQL
- UUID v4
- Model + Migration
- Controller
- Validation
- View
- CRUD: Create, Read, Update, Delete
- UUID sebagai `PRIMARY KEY`
- Password disimpan menggunakan `password_hash()`, **bukan plaintext**
- Soft delete sebagai bonus
- Struktur kode yang mudah dikembangkan

 ## 1\. Gambaran aplikasi

 Kita akan membuat aplikasi sederhana seperti ini:

```
                    ┌───────────────┐
                    │   User List   │
                    └───────┬───────┘
                            │
              ┌─────────────┼─────────────┐
              │             │             │
           Tambah         Edit          Hapus
              │             │             │
              └─────────────┼─────────────┘
                            │
                       MySQL Database
                            │
                     ┌──────┴──────┐
                     │    users    │
                     ├─────────────┤
                     │ id (UUID)   │
                     │ name        │
                     │ email       │
                     │ password    │
                     │ created_at  │
                     │ updated_at  │
                     └─────────────┘
```

 Contoh UUID yang akan menjadi ID user:

```
550e8400-e29b-41d4-a716-446655440000
```

 Jadi **bukan**:

```
1
2
3
4
```

 melainkan:

```
550e8400-e29b-41d4-a716-446655440000
7c9e6679-7425-40de-944b-e07fc1f90ae7
```

---

 # 2\. Kenapa menggunakan UUID?

 Sebelum coding, penting memahami alasannya.

 Dengan `AUTO_INCREMENT`:

```
/users/1
/users/2
/users/3
```

 Orang bisa dengan mudah menebak ID berikutnya.

 Dengan UUID:

```
/users/550e8400-e29b-41d4-a716-446655440000
```

 ID jauh lebih sulit ditebak.

 UUID juga berguna ketika aplikasi nantinya berkembang menjadi:

 - REST API
- Microservices
- Sinkronisasi database
- Distributed systems
- Import/export data
- Beberapa server/database

 Namun ada trade-off: UUID lebih besar daripada integer sehingga index/database bisa lebih berat.

 Untuk MySQL, kita bisa menyimpan UUID dalam:

```
CHAR(36)
```

 agar mudah dipahami ketika belajar.

 Untuk aplikasi dengan skala besar, nanti kita bisa optimalkan menjadi:

```
BINARY(16)
```

 Tetapi **untuk tahap belajar, `CHAR(36)` lebih nyaman**.

---

 # 3\. Membuat project CodeIgniter 4

 Kalau Composer sudah terinstall:

```
composer create-project codeigniter4/appstarter crud-user
```

 Masuk ke folder:

```
cd crud-user
```

 Jalankan development server:

```
php spark serve
```

 Kemudian buka:

```
http://localhost:8080
```

---

 # 4\. Membuat database MySQL

 Buat database:

```
CREATE DATABASE crud_user;
```

 Kemudian kita akan membuat tabel `users` melalui Migration CodeIgniter.

---

 # 5\. Konfigurasi database

 Copy:

```
env
```

 menjadi:

```
.env
```

 Kemudian buka `.env`.

 Cari konfigurasi database dan sesuaikan:

```
database.default.hostname = localhost
database.default.database = crud_user
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

 Kalau MySQL kamu menggunakan password, masukkan password-nya:

```
database.default.password = password_mysql_kamu
```

---

 # 6\. Membuat Migration User

 Jalankan:

```
php spark make:migration CreateUsersTable
```

 CodeIgniter akan membuat file migration di:

```
app/Database/Migrations/
```

 Misalnya:

```
2026-09-11-xxxxxx_CreateUsersTable.php
```

 Buka file tersebut.

 Kita buat seperti ini:

```
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],

            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
            ],

            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],

            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
```

 Perhatikan bagian ini:

```
'id' => [
    'type'       => 'CHAR',
    'constraint' => 36,
],
```

 Ini berarti UUID akan disimpan sebagai string sepanjang 36 karakter.

 Kemudian:

```
$this->forge->addKey('id', true);
```

 Parameter `true` membuat `id` menjadi **primary key**.

---

 # 7\. Jalankan migration

 Sekarang:

```
php spark migrate
```

 Kalau berhasil, database kita memiliki tabel:

```
users
```

 Strukturnya kira-kira:

```
users
├── id
├── name
├── email
├── password
├── created_at
└── updated_at
```

---

 # 8\. Membuat Model User

 Install UUID
```
composer require michalsn/codeigniter4-uuid   
```

 Buat:

```
app/Models/UserModel.php
```

 Isi:

```
<?php

namespace App\Models;

use CodeIgniter\Model;
use Michalsn\CodeIgniterUuid\Traits\HasUuid;

class UserModel extends Model
{
    use HasUuid;
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = false;

    protected $returnType = 'array';

    protected $allowedFields = [
        'id',
        'name',
        'role',
        'email',
        'password',
    ];

    protected array $casts = [
        // Menggunakan UUID v7 sebagai primary key
        'id' => 'uuid[v7]',
    ];

    protected $useTimestamps = true;
}

```

 Bagian paling penting:

```
protected $primaryKey = 'id';

protected $useAutoIncrement = false;
```

 Kenapa?

 Karena kita **tidak menggunakan**:

```
1
2
3
```

 dari MySQL `AUTO_INCREMENT`.

 ID akan kita generate sendiri menggunakan UUID.

---

Manual: Jika ingin menggunakan UUID untuk field selain primary key (misalnya tracking_id), Anda harus membuatnya secara manual menggunakan service:
```
use Michalsn\CodeIgniterUuid\Enums\UuidVersion;
$trackingId = service('uuid')->generate(UuidVersion::V4)->toRfc4122();   
```

 # 9\. Membuat UUID

 CodeIgniter 4 memiliki utility untuk UUID.

 Kita bisa menggunakan:

```
service('uuid')->uuid4()->toString()
```

 Contohnya menghasilkan:

```
9f8c5e1d-4c6a-4c11-9e5e-1f4e4b4e7c21
```

 Nah, setiap kali membuat user baru, kita generate UUID tersebut.

---

 # 10\. Membuat Controller

 Buat:

```
app/Controllers/Users.php
```

 Isi:

```
<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data User',
            'users' => $this->userModel->findAll(),
        ];

        return view('users/index', $data);
    }
}
```

 Sekarang kita sudah punya:

```
/users
```

 yang akan menampilkan seluruh user.

---

 # 11\. Membuat View

 Buat folder:

```
app/Views/users
```

 Kemudian buat:

```
app/Views/users/index.php
```

 Isi:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
</head>
<body>

    <h1><?= esc($title) ?></h1>

    <a href="<?= site_url('users/create') ?>">
        Tambah User
    </a>

    <br><br>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>UUID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['id']) ?></td>
                    <td><?= esc($user['name']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <a href="<?= site_url('users/edit/' . $user['id']) ?>">
                            Edit
                        </a>

                        |

                        <a href="<?= site_url('users/delete/' . $user['id']) ?>"
                           onclick="return confirm('Yakin ingin menghapus user ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
```

 Perhatikan kita menggunakan:

```
esc($user['name'])
```

 Ini kebiasaan yang baik untuk menghindari output HTML yang tidak diinginkan.

---

 # 12\. Membuat route

 Buka:

```
app/Config/Routes.php
```

 Tambahkan:

```
$routes->get('users', 'Users::index');
```

 Sekarang akses:

```
http://localhost:8080/users
```

 Karena database masih kosong, tabel belum memiliki data.

---

 # 13\. Membuat fitur Create

 Sekarang kita buat:

```
GET  /users/create
POST /users/store
```

 Tambahkan method berikut ke `Users.php`:

```
public function create()
{
    return view('users/create', [
        'title' => 'Tambah User',
    ]);
}
```

 Kemudian:

```
public function store()
{
    $rules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
    ];

    if (! $this->validate($rules)) {
        return redirect()
            ->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $this->userModel->insert([
        'id'       => service('uuid')->uuid4()->toString(),
        'name'     => $this->request->getPost('name'),
        'email'    => $this->request->getPost('email'),
        'password' => password_hash(
            $this->request->getPost('password'),
            PASSWORD_DEFAULT
        ),
    ]);

    return redirect()
        ->to('/users')
        ->with('success', 'User berhasil ditambahkan.');
}
```

 Nah, ini bagian yang sangat penting:

```
'id' => service('uuid')->uuid4()->toString(),
```

 Di sinilah UUID dibuat.

 Dan password:

```
password_hash(
    $this->request->getPost('password'),
    PASSWORD_DEFAULT
)
```

 **Jangan pernah menyimpan password seperti ini:**

```
'password' => $this->request->getPost('password')
```

 Karena itu berarti password disimpan plaintext.

---

 # 14\. View Create

 Buat:

```
app/Views/users/create.php
```

 Isi:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
</head>
<body>

    <h1><?= esc($title) ?></h1>

    <?php if (session()->has('errors')): ?>

        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <form action="<?= site_url('users/store') ?>" method="post">

        <?= csrf_field() ?>

        <div>
            <label>Nama</label>
            <br>
            <input
                type="text"
                name="name"
                value="<?= old('name') ?>"
            >
        </div>

        <br>

        <div>
            <label>Email</label>
            <br>
            <input
                type="email"
                name="email"
                value="<?= old('email') ?>"
            >
        </div>

        <br>

        <div>
            <label>Password</label>
            <br>
            <input
                type="password"
                name="password"
            >
        </div>

        <br>

        <button type="submit">
            Simpan
        </button>

        <a href="<?= site_url('users') ?>">
            Kembali
        </a>

    </form>

</body>
</html>
```

 Jangan lupa:

```
<?= csrf_field() ?>
```

 CSRF protection penting untuk form.

---

 # 15\. Tambahkan route Create

 Di `Routes.php`:

```
$routes->get('users', 'Users::index');

$routes->get('users/create', 'Users::create');

$routes->post('users/store', 'Users::store');
```

 Sekarang:

```
http://localhost:8080/users/create
```

 Kita sudah bisa membuat user.

---

 # 16\. Hasil di database

 Misalnya kita membuat:

```
Nama     : Budi
Email    : budi@gmail.com
Password : rahasia123
```

 Database tidak akan menyimpan:

```
id = 1
```

 tetapi:

```
id = 7f8c5e1d-4c6a-4c11-9e5e-1f4e4b4e7c21
```

 Dan password tidak menjadi:

```
rahasia123
```

 melainkan kira-kira:

```
$2y$10$...
```

 Jadi database:

```
+--------------------------------------+-------+------------------+
| id                                   | name  | email            |
+--------------------------------------+-------+------------------+
| 7f8c5e1d-4c6a-4c11-9e5e-1f4e4b4e... | Budi  | budi@gmail.com   |
+--------------------------------------+-------+------------------+
```

---

 # 17\. Membuat fitur Edit

 Tambahkan ke controller:

```
public function edit($id)
{
    $user = $this->userModel->find($id);

    if (! $user) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
            'User tidak ditemukan.'
        );
    }

    return view('users/edit', [
        'title' => 'Edit User',
        'user'  => $user,
    ]);
}
```

 Kemudian:

```
public function update($id)
{
    $user = $this->userModel->find($id);

    if (! $user) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
            'User tidak ditemukan.'
        );
    }

    $rules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
    ];

    if (! $this->validate($rules)) {
        return redirect()
            ->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $data = [
        'name'  => $this->request->getPost('name'),
        'email' => $this->request->getPost('email'),
    ];

    $password = $this->request->getPost('password');

    if (! empty($password)) {
        $data['password'] = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
    }

    $this->userModel->update($id, $data);

    return redirect()
        ->to('/users')
        ->with('success', 'User berhasil diupdate.');
}
```

 Perhatikan kita **tidak mengganti UUID ketika edit**.

 Misalnya awalnya:

```
550e8400-e29b-41d4-a716-446655440000
```

 setelah edit tetap:

```
550e8400-e29b-41d4-a716-446655440000
```

 Yang berubah hanya:

```
name
email
password
```

---

 # 18\. View Edit

 Buat:

```
app/Views/users/edit.php
```

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
</head>
<body>

    <h1><?= esc($title) ?></h1>

    <?php if (session()->has('errors')): ?>

        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <form action="<?= site_url('users/update/' . $user['id']) ?>" method="post">

        <?= csrf_field() ?>

        <div>
            <label>Nama</label>
            <br>

            <input
                type="text"
                name="name"
                value="<?= old('name', $user['name']) ?>"
            >
        </div>

        <br>

        <div>
            <label>Email</label>
            <br>

            <input
                type="email"
                name="email"
                value="<?= old('email', $user['email']) ?>"
            >
        </div>

        <br>

        <div>
            <label>Password Baru</label>
            <br>

            <input
                type="password"
                name="password"
                placeholder="Kosongkan jika tidak diubah"
            >
        </div>

        <br>

        <button type="submit">
            Update
        </button>

        <a href="<?= site_url('users') ?>">
            Kembali
        </a>

    </form>

</body>
</html>
```

 Tambahkan route:

```
$routes->get('users/edit/(:segment)', 'Users::edit/$1');

$routes->post('users/update/(:segment)', 'Users::update/$1');
```

 Mengapa menggunakan:

```
(:segment)
```

 bukan:

```
(:num)
```

 Karena ID kita **bukan angka**.

 UUID adalah:

```
550e8400-e29b-41d4-a716-446655440000
```

 Jadi:

```
(:segment)
```

 lebih tepat.

---

 # 19\. Membuat Delete

 Tambahkan:

```
public function delete($id)
{
    $user = $this->userModel->find($id);

    if (! $user) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
            'User tidak ditemukan.'
        );
    }

    $this->userModel->delete($id);

    return redirect()
        ->to('/users')
        ->with('success', 'User berhasil dihapus.');
}
```

 Route:

```
$routes->post('users/delete/(:segment)', 'Users::delete/$1');
```

 Saya sengaja lebih menyarankan **POST untuk delete**, bukan:

```
GET /users/delete/uuid
```

 Karena GET idealnya tidak digunakan untuk operasi yang mengubah data.

 Maka di view sebaiknya delete menggunakan form:

```
<form
    action="<?= site_url('users/delete/' . $user['id']) ?>"
    method="post"
    style="display:inline"
    onsubmit="return confirm('Yakin ingin menghapus user ini?')"
>
    <?= csrf_field() ?>

    <button type="submit">
        Hapus
    </button>
</form>
```

---

 # 20\. Route lengkap

 Akhirnya `Routes.php` kita menjadi:

```
$routes->get('users', 'Users::index');

$routes->get('users/create', 'Users::create');
$routes->post('users/store', 'Users::store');

$routes->get('users/edit/(:segment)', 'Users::edit/$1');
$routes->post('users/update/(:segment)', 'Users::update/$1');

$routes->post('users/delete/(:segment)', 'Users::delete/$1');
```

 Sehingga URL-nya:

```
GET  /users
GET  /users/create
POST /users/store
GET  /users/edit/{uuid}
POST /users/update/{uuid}
POST /users/delete/{uuid}
```

---

 # 21\. Struktur project

 Pada tahap ini struktur kita menjadi:

```
crud-user/
│
├── app/
│   │
│   ├── Controllers/
│   │   └── Users.php
│   │
│   ├── Database/
│   │   └── Migrations/
│   │       └── xxxx_CreateUsersTable.php
│   │
│   ├── Models/
│   │   └── UserModel.php
│   │
│   └── Views/
│       └── users/
│           ├── index.php
│           ├── create.php
│           └── edit.php
│
├── public/
│
├── writable/
│
├── .env
└── spark
```

 Ini sudah merupakan pola MVC yang cukup rapi:

```
Browser
   │
   ▼
Routes
   │
   ▼
Controller
   │
   ├──────────────► View
   │
   ▼
Model
   │
   ▼
MySQL
```

---

 # 22\. Tapi ada satu hal penting tentang UUID

 Untuk belajar, kita menggunakan:

```
CHAR(36)
```

 Namun kalau nanti aplikasi sudah besar, saya lebih menyarankan mempertimbangkan:

```
BINARY(16)
```

 Misalnya UUID:

```
550e8400-e29b-41d4-a716-446655440000
```

 disimpan secara binary 16 byte.

 Keuntungannya:

```
CHAR(36)   = 36 byte
BINARY(16) = 16 byte
```

 Index juga menjadi lebih kecil.

 Tetapi konsekuensinya query dan debugging menjadi sedikit lebih kompleks.

 Jadi untuk **belajar CodeIgniter 4 + CRUD + UUID**, kita mulai dari:

```
CHAR(36)
```

 Setelah konsepnya sudah kuat, baru kita naik level ke:

```
BINARY(16)
```

---

 # 23\. Roadmap belajar kita

 Kalau kamu benar-benar ingin belajar sampai **bisa membuat aplikasi production**, saya sarankan jangan berhenti di CRUD sederhana ini.

 Urutannya bisa kita lanjutkan seperti ini:

```
LEVEL 1
│
├── CRUD User
├── UUID
├── Migration
├── Model
├── Controller
└── Validation
        │
        ▼
LEVEL 2
│
├── Layout/template
├── Bootstrap/Tailwind
├── Flash message
├── Pagination
├── Search
└── Sorting
        │
        ▼
LEVEL 3
│
├── Login
├── Logout
├── Session
├── Password hashing
└── Authentication
        │
        ▼
LEVEL 4
│
├── Role
├── Permission
├── Admin
├── User
└── Authorization
        │
        ▼
LEVEL 5
│
├── REST API
├── JWT
├── API authentication
└── API response standard
        │
        ▼
LEVEL 6
│
├── Service Layer
├── Repository
├── Transaction
├── Events
├── Queue
└── Testing
        │
        ▼
LEVEL 7
│
├── Production security
├── Logging
├── Caching
├── Optimization
└── Deployment
```

 **Saran saya:** jangan langsung lompat ke JWT, Repository, atau arsitektur yang rumit. Kuasai dulu CRUD + UUID + validation + authentication. Setelah itu baru kita refactor menjadi struktur yang lebih profesional.

 Kalau kamu mau belajar **step-by-step seperti kursus**, kita bisa lanjut dengan **“Part 2: CRUD User CodeIgniter 4 + UUID + Bootstrap 5”**, dan saya buatkan **source code lengkap yang tinggal copy-paste**, termasuk layout, navbar, form, alert, pagination, search, dan struktur folder yang lebih profesional.
