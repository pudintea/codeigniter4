<?php

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
