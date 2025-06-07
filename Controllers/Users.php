<?php

namespace App\Controllers;

use App\Models\UsersModel;
use Irsyadulibad\DataTables\DataTables;

class Users extends BaseController
{
    protected $data;
    protected $className;
    protected $menuActive;
    protected $pdnUrl;
    protected $pdnTitle;

    public function __construct()
    {
        $this->data       = [];
        $this->className  = 'users';
        $this->menuActive = 'active';
        $this->pdnUrl     = base_url($this->className);
        $this->pdnTitle   = 'User';
    }
    public function index(): string
    {
        $this->data['pdn_title']        = 'Data '.$this->pdnTitle;
        $this->data['pdn_url']          = $this->pdnUrl;
        $this->data[$this->className]   = 'active';
        return view('users/table', $this->data);
    }

    public function tambah()
    {
        helper('form');
        $this->data['pdn_title']        = 'Tambah '.$this->pdnTitle;
        $this->data['pdn_url']          = $this->pdnUrl;
        $this->data[$this->className]   = 'active';

        $this->data['nama_lengkap'] = [
            'name'    => 'nama_lengkap',
            'id'      => 'nama_lengkap',
            'type'    => 'text',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('nama_lengkap'),
        ];
        $this->data['email'] = [
            'name'    => 'email',
            'id'      => 'email',
            'type'    => 'email',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('email'),
        ];

        $this->data['users_level'] = form_dropdown(
            'users_level',
            ['Admin' => 'Admin','Guest' => 'Guest'],
            '',
            ['class' => 'form-control', 'id' => 'users_level']
        );

        $this->data['password'] = [
            'name'    => 'password',
            'id'      => 'password',
            'type'    => 'password',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('password'),
        ];
        $this->data['password1'] = [
            'name'    => 'password1',
            'id'      => 'password1',
            'type'    => 'password',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('password1'),
        ];

        if (! $this->request->is('post')) {
            return view('users/tambah', $this->data);
        }

        $rules = [
            'nama_lengkap' => [
                'label'  => 'Nama Lengkap',
                'rules'  => 'required|min_length[3]|max_length[128]',
                'errors' => [
                    'required'    => 'Nama tidak boleh kosong.',
                    'min_length'  => 'Nama terlalu pendek.',
                    'max_length'  => 'Nama terlalu panjang.',
                ]
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|is_unique[tb_users.users_email]',
                'errors' => [
                    'required'    => 'Email harus diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[6]|matches[password1]',
                'errors' => [
                    'required'   => 'Password harus diisi.',
                    'min_length' => 'Password terlalu pendek!',
                    'matches'    => 'Password tidak cocok!',
                ]
            ],
            'password1' => [
                'label'  => 'Konfirmasi Password',
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi.',
                    'matches'  => 'Konfirmasi password tidak cocok dengan password.',
                ]
            ],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (! $this->validateData($data, $rules)) {
            return view('users/tambah', $this->data);
        }

        // Prosess Simpan jika sudah di submit dan lolos validasi
        $model = new UsersModel(); // jangan lupa di use di atas ya modelnya
        $model->insert([
            'users_nama'     => $this->request->getPost('nama_lengkap'),
            'users_email'    => $this->request->getPost('email'),
            'users_password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'users_active'   => 1,
            'users_level'    => $this->request->getPost('users_level'),
            'users_token'    => bin2hex(random_bytes(16))
        ]);

        return redirect()->to($this->className)->with('success', 'User berhasil ditambahkan.');
        
    }

    function edit($id)
    {
        $model = new UsersModel();
        $data = $model->find($id);

        helper('form');
        $this->data['pdn_title']        = 'Edit '.$this->pdnTitle;
        $this->data['pdn_url']          = $this->pdnUrl;
        $this->data[$this->className]   = 'active';

        $this->data['id'] = [
            'name'    => 'id',
            'id'      => 'id',
            'type'    => 'hidden',
            'required'=> 'required',
            'value'   => $data->id_users,
        ];

        $this->data['nama_lengkap'] = [
            'name'    => 'nama_lengkap',
            'id'      => 'nama_lengkap',
            'type'    => 'text',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('nama_lengkap', $data->users_nama),
        ];
        $this->data['email'] = [
            'name'    => 'email',
            'id'      => 'email',
            'type'    => 'email',
            'class'   => 'form-control',
            'required'=> 'required',
            'value'   => set_value('email', $data->users_email),
        ];

        $this->data['users_level'] = form_dropdown(
            'users_level',
            ['Admin' => 'Admin','Guest' => 'Guest'],
            $data->users_level,
            ['class' => 'form-control', 'id' => 'users_level']
        );
        return view('users/edit', $this->data);
    }

    public function update()
    {
        $rules = [
            'nama_lengkap' => [
                'label'  => 'Nama Lengkap',
                'rules'  => 'required|min_length[3]|max_length[128]',
                'errors' => [
                    'required'    => 'Nama tidak boleh kosong.',
                    'min_length'  => 'Nama terlalu pendek.',
                    'max_length'  => 'Nama terlalu panjang.',
                ]
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|is_unique[tb_users.users_email,id_users,'.$this->request->getPost('id').']',
                'errors' => [
                    'required'    => 'Email harus diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
                ]
            ]
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (! $this->validateData($data, $rules)) {
            $validation = \Config\Services::validation();
            
            // Jika ingin langsung menampilkan (debug)
            //echo $validation->listErrors();
        
            // Atau redirect kembali ke form dengan pesan error
            return redirect()->to($this->pdnUrl.'/edit/'.$this->request->getPost('id'))->withInput()->with('validation', $validation);
        }
        $model = new UsersModel();
        $model->update($this->request->getPost('id'), [
            'users_nama' => $this->request->getPost('nama_lengkap'),
            'users_email' => $this->request->getPost('email'),
            'users_level' => $this->request->getPost('users_level'),
        ]);

        return redirect()->to($this->pdnUrl)->with('success', 'User berhasil diubah.');
    }

    public function delete($id)
    {
        $model = new UsersModel();
        $model->delete($id);
        return redirect()->to($this->className)->with('success', 'User berhasil dihapus.');
    }

    public function data_json()
    {
        return DataTables::use('tb_users')
            ->hideColumns(['id_users','users_password','users_token','created_at','updated_at'])
            ->editColumn('users_active', function ($value, $row) {
                return $row->users_active == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function($row) {
                return '<a href="'.$this->pdnUrl.'/edit/'.$row->id_users.'" class="btn btn-sm btn-success shadow-sm" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="'.$this->pdnUrl.'/delete/'.$row->id_users.'" method="post" class="d-inline">
                            <input type="hidden" name="_method" value="DELETE">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')"><i class="fas fa-trash"></i></button>
                        </form>
                        ';
            })
            ->rawColumns(['action','users_active'])
            ->make();
    }
}
