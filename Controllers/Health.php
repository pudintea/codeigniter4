<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Health extends ResourceController
{
    public function index()
    {
        return $this->respond([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => ENVIRONMENT
        ], 200);
    }

    public function ready()
    {
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');

            return $this->respond([
                'status' => 'ok',
                'database' => 'connected'
            ], 200);

        } catch (\Throwable $e) {
            return $this->respond([
                'status' => 'error',
                'database' => 'disconnected',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}