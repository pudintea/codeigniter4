<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuratkeDiratMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'no_surat' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'surat_hal' => [
                'type' => 'TEXT'
            ],
            'surat_file' => [
                'type' => 'TEXT'
            ],
            'lampiran_1' => [
                'type' => 'TEXT'
            ],
            'lampiran_2' => [
                'type' => 'TEXT'
            ],
            'lampiran_3' => [
                'type' => 'TEXT'
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'active' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'nonactive'],
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('surat_sekolah');
    }

    public function down()
    {
        $this->forge->dropTable('surat_sekolah');
    }
}
