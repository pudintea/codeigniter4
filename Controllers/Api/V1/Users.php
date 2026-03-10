<?php
namespace App\Controllers\Api\V1;
use App\Controllers\BaseController;
use App\Models\UsersModel;

class Users extends BaseController
{
    protected $data;
    protected $format;
    protected $defaultModel;

    public function __construct()
    {
        $this->data         = [];
        $this->format       = 'json';
        $this->defaultModel = new UsersModel();
    }

    // GET /users
    public function index()
    {
        $users = $this->defaultModel->findAll();

        // filter fields: hilangkan password, created_at, updated_at
        $data = array_map(function($user) {
            unset($user['password'], $user['created_at'], $user['updated_at']);
            return $user;
        }, $users);

        return $this->successResponse($data, 'Users retrieved successfully');
    }

    // GET /users/1
    public function show($id = null)
    {
        $user = $this->defaultModel->find($id);

        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        // Filter fields sensitif
        unset($user['password'], $user['created_at'], $user['updated_at']);

        return $this->successResponse($user, 'User retrieved successfully');
    }

    // POST /users
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validation rules
        $rules = [
            'name' => [
                'label'  => 'Nama',
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'    => 'Nama tidak boleh kosong.',
                    'min_length'  => 'Nama terlalu pendek.',
                ]
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required'    => 'Email harus diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Password harus diisi.',
                    'min_length' => 'Password terlalu pendek!'
                ]
            ],
        ];

        // Validate
        if (! $this->validate($rules, $data)) {
            return $this->errorResponse(
                'Validation failed',
                $this->validator->getErrors(),
                422
            );
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Insert ke database
        if (! $this->defaultModel->insert($data)) {
            return $this->errorResponse(
                'Insert failed',
                $this->defaultModel->errors(),
                500
            );
        }

        return $this->successResponse(null, 'User created', 201);
    }

    // PUT /users/{id}
    public function update($id = null)
    {
        // Ambil user dari database
        $user = $this->defaultModel->find($id);
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        $data = $this->request->getJSON(true);

        // Validation rules
        $rules = [
            'name' => [
                'label'  => 'Nama',
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'    => 'Nama tidak boleh kosong.',
                    'min_length'  => 'Nama terlalu pendek.',
                ]
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => "required|valid_email|is_unique[users.email,id,{$id}]", // unik kecuali id sendiri
                'errors' => [
                    'required'    => 'Email harus diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'permit_empty|min_length[6]', // boleh kosong
                'errors' => [
                    'min_length' => 'Password terlalu pendek!'
                ]
            ],
            'phone' => [
                'label'  => 'Phone',
                'rules'  => 'permit_empty|min_length[6]',
                'errors' => [
                    'min_length' => 'Nomor telepon terlalu pendek'
                ]
            ]
        ];

        // Validasi
        if (! $this->validate($rules, $data)) {
            return $this->errorResponse(
                'Validation failed',
                $this->validator->getErrors(),
                422
            );
        }

        // Hash password jika ada
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']); // jangan overwrite password lama
        }

        // Update database
        if (! $this->defaultModel->update($id, $data)) {
            return $this->errorResponse(
                'Update failed',
                $this->defaultModel->errors(),
                500
            );
        }

        return $this->successResponse(null, 'User updated');
    }

    // DELETE /users/1
    public function delete($id = null)
    {
        $user = $this->defaultModel->find($id);

        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }

        $this->defaultModel->delete($id);

        return $this->successResponse(null, 'User deleted');
    }
}
