<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Bo extends BaseController
{
    public function login()
    {
        helper('form');
        if($this->request->getMethod() === 'POST'){
            // Prosess Login
            $email = $this->request->getPost('email_login');
            $password = $this->request->getPost('password_login');

            $rules = [
                'email_login' => [
                    'label'  => 'Email',
                    'rules'  => 'required|valid_email|is_not_unique[tb_users.users_email]',
                    'errors' => [
                        'required'    => 'Email harus diisi.',
                        'valid_email' => 'Format email tidak valid.',
                        'is_not_unique'   => 'Email tidak terdaftar.',
                    ]
                ],
                'password_login' => [
                    'label'  => 'Password',
                    'rules'  => 'required|min_length[8]',
                    'errors' => [
                        'required'   => 'Password harus diisi.',
                        'min_length' => 'Password terlalu pendek!',
                    ]
                ],
            ];
        
            $data = $this->request->getPost(array_keys($rules));

            if (! $this->validateData($data, $rules)) {
                $validation = \Config\Services::validation();
                // Jika ingin langsung menampilkan (debug)
                //echo $validation->listErrors();
                // Atau redirect kembali ke form dengan pesan error
                return redirect()->to('login')->withInput()->with('validation', $validation);
            }

            $modelUser = new UsersModel();
            $user = $modelUser->where('users_email', $email)->first();

            if ($user && password_verify($password, $user->users_password)){
                //Jika daa user ada dan password sesuai
                // Buat Session
                session()->set([
                    'is_login' => true,
                    'pdn_email' => $user->users_email,
                    'pdn_nama' => $user->users_nama,
                    'pdn_level' => $user->users_level,
                    'pdn_token' => $user->users_token,                
                ]);
                return redirect()->to('/dashboard')->with('success', 'Selamat anda sudah berhasil login.');
                
            }else{
                // Jika user tidak ada dan password salah
                return redirect()->to('/login')->with('error', 'Maaf, Email dan Password anda tidak sesuai.');
            }
        }

        return view('bo/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
