<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SekolahMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'nama_sekolah' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'email_sekolah' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'alamat' => [
                'type' => 'TEXT'
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
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
        $this->forge->createTable('sekolah');
    }

    public function down()
    {
        $this->forge->dropTable('sekolah');
    }
}
