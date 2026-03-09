<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SuratkeSekolahShareMigration extends Migration
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
            'id_surat_dirat' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'id_sekolah' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
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
        $this->forge->createTable('surat_dirat_share');
    }

    public function down()
    {
        $this->forge->dropTable('surat_dirat_share');
    }
}
