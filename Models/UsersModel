<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'tb_users';
    protected $primaryKey       = 'id_users';
    protected $returnType       = 'object';
    protected $allowedFields    = ['users_nama', 'users_email', 'users_password', 'users_active', 'users_level', 'users_token'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
