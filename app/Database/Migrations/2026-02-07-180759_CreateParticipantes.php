<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre_completo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
                'null' => false,
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'edad' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => false,
            ],
            'genero' => [
                'type' => 'ENUM',
                'constraint' => ['M', 'F', 'Otro'],
                'null' => false,
            ],
            'categoria' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ],
            'talla_playera' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'validado', 'cancelado'],
                'default' => 'pendiente',
                'null' => false,
            ],
            'fecha_registro' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('participantes', true);
    }

    public function down()
    {
        $this->forge->dropTable('participantes', true);
    }
}
