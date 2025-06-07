<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use Irsyadulibad\DataTables\DataTables;
use CodeIgniter\HTTP\ResponseInterface;

class UsersController extends BaseController
{
    protected $data;
    protected $className;
    protected $menuActive;
    protected $pdnTitle;

    public function __construct()
    {
        $this->data       = [];
        $this->menuActive = 'menuusers';
    }

    public function index()
    {
        $this->data['pdn_title']         = 'Data User';
        $this->data[$this->menuActive]   = 'active';
        return view('users/content', $this->data);
    }
    // SIMPAN DATA
    public function tambah()
    {
        helper('form');
        $this->data['pdn_title']         = 'Data User';
        $this->data[$this->menuActive]   = 'active';

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
        }else{
            // Prosess POST Simpan data
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
                        'matches'  => 'Password tidak cocok dengan konfirmasi password.',
                    ]
                ],
            ];

            $data_req = $this->request->getPost(array_keys($rules));
            if (! $this->validateData($data_req, $rules)) {
                // Kembalikan dan berikan informasi errornya
                return view('users/tambah', $this->data);
            }else{
                // Prosess Simpan Data
                $simpan_data = [
                    'users_nama'     => $this->request->getPost('nama_lengkap'),
                    'users_email'    => $this->request->getPost('email'),
                    'users_password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'users_active'   => '1',
                    'users_level'    => $this->request->getPost('users_level'),
                    'users_token'    => bin2hex(random_bytes(16))
                ];
                // Panggil model
                $usersModel = new UsersModel();
                // Simpan Data
                $proses_simpan = $usersModel->insert($simpan_data);
                if ($proses_simpan){
                    // Prosess Simpan data berhasil
                    return redirect()->to('users')->with('success','Data berhasil disimpan.');
                }else{
                    //Simpan data tidak berhasil
                    return redirect()->to('users')->with('error','Maaf, Data tidak berhasil disimpan.');
                }
            }
        }
    }
    // EDIT DATA
    public function edit($id)
    {
        if ($id === null) {
        // Redirect atau tampilkan error
        return redirect()->to('/users')->with('error', 'ID tidak ditemukan');
        }

        $model = new UsersModel();
        $data = $model->find($id);

        helper('form');
        $this->data['pdn_title']        = 'Edit '.$this->pdnTitle;
        $this->data[$this->menuActive]  = 'active';
        $this->data['update_id']        = $data->id_users;

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

        if (! $this->request->is('post')) {
            return view('users/edit', $this->data);
        }else{
            // PROSESS UPDATE DATA
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

            $data_req = $this->request->getPost(array_keys($rules));

            if (! $this->validateData($data_req, $rules)) {
                // Jika Error dana Role Update
                return view('users/edit', $this->data);
            }else{
                // Jika tidak ada masalah, lanjut ke prosess simpan data

                $data_update = [
                    'users_nama' => $this->request->getPost('nama_lengkap'),
                    'users_email' => $this->request->getPost('email'),
                    'users_level' => $this->request->getPost('users_level'),
                ];
                $usersModel = new UsersModel();
                $diUpdate = $usersModel->update($this->request->getPost('id'), $data_update);
                if($diUpdate){
                    // Jika prosess Update Lancar
                    return redirect()->to('users')->with('success','Data berhasil diupdate.');
                }else{
                    // Jika Prosess Update Bermasalah
                    return redirect()->to('users')->with('error','Maaf, Data tidak berhasil diupdate.');
                }
            }
        }
        
    }
    // HAPUS DATA
    public function hapus($id)
    {
        $model = new UsersModel();
        $model->delete($id);
        return redirect()->to('users')->with('success', 'User berhasil dihapus.');
    }
    // JOSN DATATBLES
    public function data_json()
    {
        return DataTables::use('tb_users')
            ->hideColumns(['id_users','users_password','users_token','created_at','updated_at'])
            ->editColumn('users_active', function ($value, $row) {
                return $row->users_active == 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function($row) {
                return '<a href="/users/edit/'.$row->id_users.'" class="btn btn-sm btn-success shadow-sm" title="Edit">Edit</a>
                        <form action="/users/hapus/'.$row->id_users.'" method="post" class="d-inline">
                            <input type="hidden" name="_method" value="DELETE">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Hapus" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">Hapus</button>
                        </form>
                        ';
            })
            ->rawColumns(['action','users_active'])
            ->make();
    }

    
}
