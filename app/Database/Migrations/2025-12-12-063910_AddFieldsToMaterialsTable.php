<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToMaterialsTable extends Migration
{
    public function up()
    {
        $fields = [
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'file_path'
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'file_type'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'file_size'
            ]
        ];

        $this->forge->addColumn('materials', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('materials', 'file_type');
        $this->forge->dropColumn('materials', 'file_size');
        $this->forge->dropColumn('materials', 'description');
    }
}
