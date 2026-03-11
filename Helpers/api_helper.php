<?php

/**
 * API RESPONSE STANDAR BY PUDIN
 */
function successResponse($data = null, $message = 'Success', $code = 200)
{
    return $this->response->setJSON([
        'status'    => true,
        'code'      => $code,
        'message'   => $message,
        'data'      => $data,
        'errors'    => null
    ])->setStatusCode($code);
}

function errorResponse($message = 'Error', $errors = null, $code = 400)
{
    return $this->response->setJSON([
        'status'    => false,
        'code'      => $code,
        'message'   => $message,
        'data'      => null,
        'errors'    => $errors
    ])->setStatusCode($code);
}

/*
Daftarkan Helper
Edit file:
app/Config/Autoload.php
Cari bagian:
public $helpers = [];
Ubah menjadi:
public $helpers = ['api'];
*/
