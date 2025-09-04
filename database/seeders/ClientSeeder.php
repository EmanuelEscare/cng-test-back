<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clients')->insert([
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'phone' => '5551234567',
                'address' => 'Av. Reforma 123, CDMX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'María López',
                'email' => 'maria.lopez@example.com',
                'phone' => '5559876543',
                'address' => 'Calle Juárez 45, Guadalajara',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Sánchez',
                'email' => 'carlos.sanchez@example.com',
                'phone' => '5552223333',
                'address' => 'Col. Centro, Monterrey',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ana Torres',
                'email' => 'ana.torres@example.com',
                'phone' => '5554445555',
                'address' => 'Zona Rosa, CDMX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis García',
                'email' => 'luis.garcia@example.com',
                'phone' => '5556667777',
                'address' => 'Calle Hidalgo 90, Puebla',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sofía Ramírez',
                'email' => 'sofia.ramirez@example.com',
                'phone' => '5558889999',
                'address' => 'Av. Universidad, Querétaro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diego Fernández',
                'email' => 'diego.fernandez@example.com',
                'phone' => '5553334444',
                'address' => 'Col. Americana, Guadalajara',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lucía Morales',
                'email' => 'lucia.morales@example.com',
                'phone' => '5551112222',
                'address' => 'San Pedro, Monterrey',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jorge Hernández',
                'email' => 'jorge.hernandez@example.com',
                'phone' => '5557778888',
                'address' => 'Col. Roma, CDMX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paola Martínez',
                'email' => 'paola.martinez@example.com',
                'phone' => '5559990000',
                'address' => 'Col. Del Valle, CDMX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
