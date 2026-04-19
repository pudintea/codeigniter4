<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class Datatables extends Model
{
    protected $table;
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];
    protected $where = [];
    protected $request;
    protected $db;
    protected $dt;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
    }

    /**
     * Set konfigurasi datatables secara dinamis
     */
    public function setConfig($table, $column_order = [], $column_search = [], $order = [], $where=[])
    {
        $this->table = $table;
        $this->column_order = $column_order;
        $this->column_search = $column_search;
        $this->order = $order;
        $this->where = $where; 

        $this->dt = $this->db->table($this->table);

        return $this;
    }

    /**
     * Set kondisi where (opsional)
     */
    public function setWhere($where = [])
    {
        if (!empty($where)) {
            $this->where = $where;
        }
        return $this; // biar bisa chaining
    }

    private function _get_datatables_query()
    {
        // RESET builder setiap query
        $this->dt = $this->db->table($this->table);

        $searchValue = $this->request->getPost('search')['value'] ?? null;
        $orderData = $this->request->getPost('order')[0] ?? null;

        // WHERE
        if (!empty($this->where)) {
            $this->dt->where($this->where);
        }

        // SEARCH
        if ($searchValue) {
            $this->dt->groupStart();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $this->dt->like($item, $searchValue);
                } else {
                    $this->dt->orLike($item, $searchValue);
                }
            }
            $this->dt->groupEnd();
        }

        // ORDER
        if ($orderData) {
            $colIndex = $orderData['column'];
            $dir = $orderData['dir'];
            if (isset($this->column_order[$colIndex])) {
                $this->dt->orderBy($this->column_order[$colIndex], $dir);
            }
        } else if (!empty($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();

        if ($this->request->getPost('length') != -1) {
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        }

        return $this->dt->get()->getResult();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->dt->countAllResults();
    }

    public function count_all()
    {
        $builder = $this->db->table($this->table);
        if (!empty($this->where)) {
            $builder->where($this->where);
        }
        return $builder->countAllResults();
    }
}