<?php

if (!function_exists('pdn_bulan')) {
    function pdn_bulan($bln)
    {
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        return $namaBulan[(int)$bln] ?? '';
    }
}

if (!function_exists('pdn_tgl_indo')) {
    function pdn_tgl_indo($tgl)
    {
        $pecah = explode('-', $tgl);
        if (count($pecah) < 3) return $tgl;

        $tanggal = $pecah[2];
        $bulan   = pdn_bulan($pecah[1]);
        $tahun   = $pecah[0];

        return $tanggal . ' ' . $bulan . ' ' . $tahun;
    }
}

if (!function_exists('pdn_tgl_default')) {
    function pdn_tgl_default($tgl)
    {
        $tanggal    = date('d-m-Y', strtotime($tgl));
        return $tanggal;
    }
}

if (!function_exists('pdn_nama_hari')) {
    function pdn_nama_hari($tanggal)
    {
        $timestamp = strtotime($tanggal);
        $nama = date("l", $timestamp);
        $hari = [
            "Sunday" => "Ahad",
            "Monday" => "Senin",
            "Tuesday" => "Selasa",
            "Wednesday" => "Rabu",
            "Thursday" => "Kamis",
            "Friday" => "Jumat",
            "Saturday" => "Sabtu"
        ];
        return $hari[$nama] ?? '';
    }
}

if (!function_exists('pdn_kode_hari')) {
    function pdn_kode_hari($tanggal)
    {
        $nama = date("l", strtotime($tanggal));
        $kode = [
            "Monday" => "1",
            "Tuesday" => "2",
            "Wednesday" => "3",
            "Thursday" => "4",
            "Friday" => "5",
            "Saturday" => "6",
            "Sunday" => "7"
        ];
        return $kode[$nama] ?? '';
    }
}

if (!function_exists('pdn_hari_kode')) {
    function pdn_hari_kode($no_kode)
    {
        $hari = [
            "1" => "Senin",
            "2" => "Selasa",
            "3" => "Rabu",
            "4" => "Kamis",
            "5" => "Jumat",
            "6" => "Sabtu",
            "7" => "Ahad"
        ];
        return $hari[$no_kode] ?? '';
    }
}

if (!function_exists('pdn_hitung_mundur')) {
    function pdn_hitung_mundur($wkt)
    {
        $waktu = [
            365 * 24 * 60 * 60 => "tahun",
            30 * 24 * 60 * 60  => "bulan",
            7 * 24 * 60 * 60   => "minggu",
            24 * 60 * 60       => "hari",
            60 * 60            => "jam",
            60                 => "menit",
            1                  => "detik"
        ];

        $selisih = time() - $wkt;
        if ($selisih < 5) {
            return 'kurang dari 5 detik yang lalu';
        }

        $hasil = [];
        $stop = 0;
        foreach ($waktu as $periode => $satuan) {
            if ($stop >= 6 || ($stop > 0 && $periode < 60)) break;
            $bagi = floor($selisih / $periode);
            if ($bagi > 0) {
                $hasil[] = $bagi . ' ' . $satuan;
                $selisih -= $bagi * $periode;
                $stop++;
            } else if ($stop > 0) {
                $stop++;
            }
        }

        return implode(' ', $hasil) . ' yang lalu';
    }
}

if (!function_exists('pdn_getRole')) {
    function pdn_getRoles(): array
    {
        return [
            'admin'     => 'Admin',
            'guru'      => 'Guru',
            'orang_tua' => 'Orang Tua'
        ];
    }
}

if (!function_exists('pdn_getKelasSD')) {
    function pdn_getKelasSD(): array
    {
        return [
            'Kelas 1'     => 'Kelas 1',
            'Kelas 2'     => 'Kelas 2',
            'Kelas 3'     => 'Kelas 3',
            'Kelas 4'     => 'Kelas 4',
            'Kelas 5'     => 'Kelas 5',
            'Kelas 6'     => 'Kelas 6'
        ];
    }
}

if (!function_exists('pdn_getKelasSMP')) {
    function pdn_getKelasSMP(): array
    {
        return [
            'Kelas 7'     => 'Kelas 7',
            'Kelas 8'     => 'Kelas 8',
            'Kelas 9'     => 'Kelas 9'
        ];
    }
}

if (!function_exists('pdn_getKelasSMA')) {
    function pdn_getKelasSMA(): array
    {
        return [
            'Kelas 10'    => 'Kelas 10',
            'Kelas 11'    => 'Kelas 11',
            'Kelas 12'    => 'Kelas 12'
        ];
    }
}

// ==================== HELPER CODEIGNITER 4 =========================
