<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Personas;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Personas::create([
            'title' => 'Persona Pertama',
            'description' => 'Mahasiswa yang belajar pemrograman biasanya memiliki motivasi untuk memahami logika dan menyelesaikan masalah teknis melalui kode. Mahasiswa ini sering memanfaatkan sumber daya online seperti tutorial, video, dan forum diskusi untuk mendukung proses belajar. Mahasiswa ini cenderung belajar secara mandiri, namun terkadang menghadapi tantangan dalam memahami konsep abstrak seperti algoritma, struktur data, atau debugging. Mahasiswa ini menghargai penjelasan yang langsung, disertai contoh praktis, dan merasa termotivasi ketika melihat hasil kerja dalam bentuk program yang berjalan dengan baik. Selain itu, mahasiswa ini sering menggunakan waktu luang untuk latihan melalui proyek kecil atau tantangan pemrograman guna meningkatkan keterampilan',
            'ai_prompt' => 'Kamu adalah tutor AI yang membantu mahasiswa dengan persona berikut:',
            'is_active' => true,
        ]);

        Personas::create([
            'title' => 'Persona Kedua',
            'description' => 'Mahasiswa ini memiliki goal menjadi pengembang aplikasi berbasis web dalam waktu satu tahun. Mahasiswa ini membutuhkan pemahaman mendalam tentang dasar-dasar pemrograman seperti logika, algoritma, dan debugging, serta ingin menguasai bahasa JavaScript dan framework populer seperti React. Tantangan utama yang dihadapi adalah sulitnya memvisualisasikan bagaimana konsep abstrak diterapkan pada proyek nyata dan manajemen waktu karena padatnya jadwal kuliah. Motivasi utama mahasiswa ini adalah keinginan untuk menciptakan produk digital yang berguna dan meningkatkan prospek karier. Strategi belajar yang digunakan meliputi mengikuti kursus online terstruktur, mengerjakan proyek kecil secara mandiri, dan aktif bertanya di komunitas pengembang. Mahasiswa ini memahami konsep dasar pemrograman, seperti variabel, loop, dan fungsi, namun masih membutuhkan latihan dalam menerapkan konsep tersebut ke dalam proyek nyata',
            'ai_prompt' => 'Kamu adalah tutor AI yang membantu mahasiswa dengan persona berikut:',
            'is_active' => true,
        ]); 
    }
}
