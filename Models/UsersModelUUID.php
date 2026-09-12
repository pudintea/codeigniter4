<?php

namespace App\Models;

use CodeIgniter\Model;
use Michalsn\CodeIgniterUuid\Traits\HasUuid;

class UsersModel extends Model
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
