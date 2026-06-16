<?php
        // SIMPLE
        // DATA KATEGORI PROSDUK
        $this->kategoriModel->orderBy('nama_kategori_produk', 'asc');
        $jenjang_options = $this->kategoriModel->findAll();
        $jenjang_options = array_column($jenjang_options, 'nama_kategori_produk', 'id');

        $this->data['kategori_produk'] = form_dropdown(
            'kategori_produk',
            $jenjang_options,
             set_value('kategori_produk'),
            ['class' => 'form-control', 'id' => 'kategori_produk']
        );
        // END KATEGORI PROSDUK

        // LENGKAP
        // KELAS
        $this->kelasModel->orderBy('kelas_nama', 'ASC');
        $kelas_data = $this->kelasModel->findAll();

        $kelas_options = [];

        foreach ($kelas_data as $row) {
            $kelas_options[$row['id']] = $row['kelas_nama'] . ' (' . $row['kelas_subnama'] . ')';
        }

        $this->data['kelas'] = form_dropdown(
            'kelas',
            $kelas_options,
            set_value('kelas'),
            [
                'class' => 'form-control',
                'id'    => 'kelas'
            ]
        );
        // END KELAS
