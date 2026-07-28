<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UsersService;

class UsersController extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar User',
            'users' => $this->userService->getAllUsers()
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        return view('users/create', ['title' => 'Tambah User Baru']);
    }

    public function store()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'username'     => 'required|is_unique[users.username]',
            'password'     => 'required|min_length[6]',
            'role'         => 'required|in_list[admin,tu,kepsek]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userService->createUser($this->request->getPost());

        return redirect()->to('/users')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User tidak ditemukan.");
        }

        return view('users/edit', [
            'title' => 'Edit User',
            'user'  => $user
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'username'     => "required|is_unique[users.username,id,{$id}]",
            'role'         => 'required|in_list[admin,tu,kepsek]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userService->updateUser($id, $this->request->getPost());

        return redirect()->to('/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->userService->deleteUser($id);
        return redirect()->to('/users')->with('success', 'Data user berhasil dihapus.');
    }
}
