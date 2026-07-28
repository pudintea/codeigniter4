<?php

namespace App\Services;

use App\Models\UsersModel;

class UsersService
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UsersModel();
    }


    public function getAllUsers()
    {
        return $this->userModel->findAll();
    }

    public function getUserById($id)
    {
        return $this->userModel->find($id);
    }

    public function createUser(array $data)
    {
        // Hash password menggunakan bcrypt
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        // Buat token unik otomatis
        $data['token'] = bin2hex(random_bytes(16));

        return $this->userModel->insert($data);
    }

    public function updateUser($id, array $data)
    {
        // Jika password diisi, hash password baru
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Jangan ubah password jika kolom input dikosongkan
            unset($data['password']);
        }

        return $this->userModel->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->userModel->delete($id); // Soft delete
    }
}
