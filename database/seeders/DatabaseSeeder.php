<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = now()->year.'/'.(now()->year + 1);

        // Disable foreign key checks for clean truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('schedule_exceptions')->truncate();
        DB::table('aspirations')->truncate();
        DB::table('report_status_histories')->truncate();
        DB::table('reports')->truncate();
        DB::table('reservations')->truncate();
        DB::table('notifications')->truncate();
        DB::table('assignment_submissions')->truncate();
        DB::table('assignments')->truncate();
        DB::table('materials')->truncate();
        DB::table('schedules')->truncate();
        DB::table('course_class_student')->truncate();
        DB::table('course_classes')->truncate();
        DB::table('cohorts')->truncate();
        DB::table('courses')->truncate();
        DB::table('rooms')->truncate();
        DB::table('floors')->truncate();
        DB::table('buildings')->truncate();
        DB::table('users')->truncate();
        DB::table('study_programs')->truncate();
        DB::table('departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Departments
        $deptTI = DB::table('departments')->insertGetId([
            'code' => 'TI',
            'name' => 'Jurusan Teknologi Informasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $deptTE = DB::table('departments')->insertGetId([
            'code' => 'TE',
            'name' => 'Jurusan Teknik Elektro',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Study Programs
        $spIF = DB::table('study_programs')->insertGetId([
            'department_id' => $deptTI,
            'code' => 'IF',
            'name' => 'Teknik Informatika (S1)',
            'level' => 'S1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $spTRK = DB::table('study_programs')->insertGetId([
            'department_id' => $deptTI,
            'code' => 'TRK',
            'name' => 'Teknologi Rekayasa Komputer (D4)',
            'level' => 'D4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $spTIM = DB::table('study_programs')->insertGetId([
            'department_id' => $deptTI,
            'code' => 'TIM',
            'name' => 'Teknik Informatika Multimedia',
            'level' => 'D4',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $spTE = DB::table('study_programs')->insertGetId([
            'department_id' => $deptTE,
            'code' => 'TL',
            'name' => 'Teknik Listrik (D3)',
            'level' => 'D3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Users (Admin, Dosen, Mahasiswa)
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin SAKALA',
            'email' => 'admin@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nim_nip' => '1000000001',
            'department_id' => $deptTI,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dosenBudi = DB::table('users')->insertGetId([
            'name' => 'Dr. Budi Santoso, M.Kom',
            'email' => 'dosen@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim_nip' => '198001012005011002',
            'department_id' => $deptTI,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dosenSiti = DB::table('users')->insertGetId([
            'name' => 'Siti Aminah, M.Kom',
            'email' => 'siti@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim_nip' => '198503152010122001',
            'department_id' => $deptTI,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dosenHendra = DB::table('users')->insertGetId([
            'name' => 'Ir. Hendra Wijaya, M.T',
            'email' => 'hendra@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim_nip' => '197808202003121003',
            'department_id' => $deptTI,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mhsHaikal = DB::table('users')->insertGetId([
            'name' => 'Haikal',
            'email' => 'mahasiswa@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nip' => '2105123456',
            'department_id' => $deptTI,
            'study_program_id' => $spTIM,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mhsAndi = DB::table('users')->insertGetId([
            'name' => 'Andi Pratama',
            'email' => 'andi@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nip' => '2105123457',
            'department_id' => $deptTI,
            'study_program_id' => $spTIM,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mhsCitra = DB::table('users')->insertGetId([
            'name' => 'Citra Dewi',
            'email' => 'citra@sakala.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nip' => '2105123458',
            'department_id' => $deptTI,
            'study_program_id' => $spTRK,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Buildings & Rooms
        $gedungTI_A = DB::table('buildings')->insertGetId([
            'code' => 'TI-A',
            'name' => 'Gedung Teknologi Informasi A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $gedungTI_B = DB::table('buildings')->insertGetId([
            'code' => 'TI-B',
            'name' => 'Gedung Teknologi Informasi B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $gedungTE = DB::table('buildings')->insertGetId([
            'code' => 'TE',
            'name' => 'Gedung Teknik Elektro',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gedung TI-A Rooms
        $roomLabMulti = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'LAB-TI1',
            'name' => 'Lab Multimedia',
            'capacity' => 35,
            'type' => 'Laboratorium',
            'floor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomLabRek = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'LAB-TI2',
            'name' => 'Lab Rekayasa Komputer',
            'capacity' => 30,
            'type' => 'Laboratorium',
            'floor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRK2A = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'RK-2A',
            'name' => 'Ruang Kuliah 2A',
            'capacity' => 40,
            'type' => 'Kelas',
            'floor' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRK2B = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'RK-2B',
            'name' => 'Ruang Kuliah 2B',
            'capacity' => 40,
            'type' => 'Kelas',
            'floor' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRK3A = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'RK-3A',
            'name' => 'Ruang Kuliah 3A',
            'capacity' => 40,
            'type' => 'Kelas',
            'floor' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomLabCloud = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'LAB-TI3',
            'name' => 'Lab Jaringan & Cloud',
            'capacity' => 32,
            'type' => 'Laboratorium',
            'floor' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRK4A = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'RK-4A',
            'name' => 'Ruang Kuliah 4A',
            'capacity' => 45,
            'type' => 'Kelas',
            'floor' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRK4B = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_A,
            'code' => 'RK-4B',
            'name' => 'Studio Riset & Skripsi',
            'capacity' => 25,
            'type' => 'Laboratorium',
            'floor' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gedung TI-B Rooms
        $roomDiskusi = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_B,
            'code' => 'R-DISC',
            'name' => 'Ruang Diskusi Mandiri',
            'capacity' => 20,
            'type' => 'Kelas',
            'floor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomAula = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTI_B,
            'code' => 'AULA-TI',
            'name' => 'Aula Gedung TI',
            'capacity' => 120,
            'type' => 'Aula',
            'floor' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gedung TE Rooms
        $roomLabKendali = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTE,
            'code' => 'LAB-TE1',
            'name' => 'Lab Kendali & Robotika',
            'capacity' => 25,
            'type' => 'Laboratorium',
            'floor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomRKTE2 = DB::table('rooms')->insertGetId([
            'building_id' => $gedungTE,
            'code' => 'RK-TE2',
            'name' => 'Ruang Kuliah Elektro 2',
            'capacity' => 35,
            'type' => 'Kelas',
            'floor' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (DB::table('rooms')->select('building_id', 'floor')->distinct()->get() as $roomFloor) {
            $floorId = DB::table('floors')->insertGetId([
                'building_id' => $roomFloor->building_id,
                'number' => $roomFloor->floor,
                'label' => 'Lantai '.$roomFloor->floor,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('rooms')
                ->where('building_id', $roomFloor->building_id)
                ->where('floor', $roomFloor->floor)
                ->update(['floor_id' => $floorId]);
        }

        // 5. Courses & Classes
        $cWeb = DB::table('courses')->insertGetId([
            'code' => 'TI-401',
            'name' => 'Pemrograman Web',
            'credits' => 3,
            'semester' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cDB = DB::table('courses')->insertGetId([
            'code' => 'TI-402',
            'name' => 'Basis Data Lanjut',
            'credits' => 3,
            'semester' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cRPL = DB::table('courses')->insertGetId([
            'code' => 'TI-403',
            'name' => 'Rekayasa Perangkat Lunak',
            'credits' => 3,
            'semester' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cJaringan = DB::table('courses')->insertGetId([
            'code' => 'TI-404',
            'name' => 'Praktikum Jaringan Komputer',
            'credits' => 2,
            'semester' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Classes
        $classWebA = DB::table('course_classes')->insertGetId([
            'course_id' => $cWeb,
            'lecturer_id' => $dosenBudi,
            'name' => 'TIM 5A',
            'academic_year' => $academicYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classWebB = DB::table('course_classes')->insertGetId([
            'course_id' => $cWeb,
            'lecturer_id' => $dosenBudi,
            'name' => 'TIM 5B',
            'academic_year' => $academicYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classDbA = DB::table('course_classes')->insertGetId([
            'course_id' => $cDB,
            'lecturer_id' => $dosenSiti,
            'name' => 'TIM 5A',
            'academic_year' => $academicYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classRplA = DB::table('course_classes')->insertGetId([
            'course_id' => $cRPL,
            'lecturer_id' => $dosenBudi,
            'name' => 'TIM 5A',
            'academic_year' => $academicYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classJarA = DB::table('course_classes')->insertGetId([
            'course_id' => $cJaringan,
            'lecturer_id' => $dosenHendra,
            'name' => 'TIM 5A',
            'academic_year' => $academicYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call(AdditionalCourseClassesSeeder::class);

        $cohortTim5A = DB::table('cohorts')->where('name', 'TIM 5A')->value('id');
        $cohortTrk5A = DB::table('cohorts')->where('name', 'TRK 5A')->value('id');
        DB::table('users')->whereIn('id', [$mhsHaikal, $mhsAndi])->update(['cohort_id' => $cohortTim5A]);
        DB::table('users')->where('id', $mhsCitra)->update(['cohort_id' => $cohortTrk5A]);

        // 6. Schedules
        DB::table('schedules')->insert([
            [
                'course_class_id' => $classWebA,
                'room_id' => $roomLabMulti,
                'day' => 'Senin',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'mode' => 'ONSITE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_class_id' => $classDbA,
                'room_id' => $roomRK2A,
                'day' => 'Senin',
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'mode' => 'ONSITE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_class_id' => $classRplA,
                'room_id' => $roomRK2B,
                'day' => 'Senin',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00',
                'mode' => 'ONLINE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_class_id' => $classJarA,
                'room_id' => $roomLabRek,
                'day' => 'Selasa',
                'start_time' => '08:00:00',
                'end_time' => '11:00:00',
                'mode' => 'ONSITE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_class_id' => $classWebB,
                'room_id' => $roomLabMulti,
                'day' => 'Selasa',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00',
                'mode' => 'ONSITE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $cancelledScheduleId = DB::table('schedules')
            ->where('course_class_id', $classWebB)
            ->where('day', 'Selasa')
            ->value('id');
        DB::table('schedule_exceptions')->insert([
            'schedule_id' => $cancelledScheduleId,
            'date' => now()->next('Tuesday')->format('Y-m-d'),
            'cancelled_by' => $dosenBudi,
            'reason' => 'Dosen berhalangan hadir pada tanggal ini.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Materials
        DB::table('materials')->insert([
            [
                'course_class_id' => $classWebA,
                'title' => 'Pengenalan HTML5, CSS3, dan Modern Layouts',
                'description' => 'Materi pengantar dasar-dasar sintaks web semantik, Flexbox, dan CSS Grid.',
                'file_path' => 'materials/modul1_html_css.pdf',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'course_class_id' => $classWebA,
                'title' => 'RESTful API & MVC Pattern di Laravel',
                'description' => 'Konsep routing, controller, Eloquent ORM, dan resource collection.',
                'file_path' => 'materials/modul2_laravel_api.pdf',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'course_class_id' => $classDbA,
                'title' => 'Normalisasi Database & Indexing Strategy',
                'description' => 'Panduan normalisasi 1NF sampai 3NF serta optimasi query menggunakan B-Tree indexes.',
                'file_path' => 'materials/modul1_database_optimization.pdf',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'course_class_id' => $classRplA,
                'title' => 'Software Requirement Specification (SRS) & Agile Methodology',
                'description' => 'Format standar dokumentasi kebutuhan perangkat lunak (IEEE 830) dan tahapan sprint.',
                'file_path' => 'materials/modul1_srs_agile.pdf',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ]);

        // 8. Assignments
        $assignWeb1 = DB::table('assignments')->insertGetId([
            'course_class_id' => $classWebA,
            'title' => 'Tugas REST API - Laravel',
            'description' => 'Buat API CRUD untuk sistem manajemen inventaris barang kampus lengkap dengan validasi request dan token authentication.',
            'deadline' => now()->addDays(2),
            'file_path' => 'assignments/brief_tugas_rest_api.pdf',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);

        $assignDb1 = DB::table('assignments')->insertGetId([
            'course_class_id' => DB::table('course_classes')
                ->where('course_id', $cDB)
                ->where('cohort_id', $cohortTrk5A)
                ->value('id'),
            'title' => 'ER Diagram Sistem Perpustakaan',
            'description' => 'Rancang ERD konseptual dan fisik untuk studi kasus perpustakaan digital terdistribusi.',
            'deadline' => now()->addDays(4),
            'file_path' => 'assignments/brief_erd_library.pdf',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $assignRpl1 = DB::table('assignments')->insertGetId([
            'course_class_id' => $classRplA,
            'title' => 'Dokumen SRS Proyek Akhir',
            'description' => 'Susun dokumen SRS proyek akhir tim berdasarkan template yang disediakan.',
            'deadline' => now()->addDays(7),
            'file_path' => 'assignments/template_srs.pdf',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        // 9. Assignment Submissions
        DB::table('assignment_submissions')->insert([
            [
                'assignment_id' => $assignWeb1,
                'student_id' => $mhsHaikal,
                'file_path' => 'submissions/haikal_tugas_api.zip',
                'score' => 95,
                'feedback' => 'Arsitektur controller dan service layer sangat baik. Dokumentasi endpoint jelas.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'assignment_id' => $assignWeb1,
                'student_id' => $mhsAndi,
                'file_path' => 'submissions/andi_tugas_api.zip',
                'score' => 88,
                'feedback' => 'Bagus, pastikan respon error konsisten dalam format JSON.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'assignment_id' => $assignDb1,
                'student_id' => $mhsCitra,
                'file_path' => 'submissions/citra_erd.pdf',
                'score' => null,
                'feedback' => null,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
        ]);

        // 10. Reservations
        DB::table('reservations')->insert([
            [
                'user_id' => $mhsHaikal,
                'room_id' => $roomLabRek,
                'date' => now()->addDays(1)->format('Y-m-d'),
                'start_time' => '13:00:00',
                'end_time' => '15:00:00',
                'purpose' => 'Praktikum Mandiri & Diskusi BEM',
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => $mhsAndi,
                'room_id' => $roomRK2B,
                'date' => now()->addDays(2)->format('Y-m-d'),
                'start_time' => '15:00:00',
                'end_time' => '17:00:00',
                'purpose' => 'Diskusi Kelompok Final Project',
                'status' => 'approved',
                'admin_notes' => 'Disetujui. Harap menjaga kebersihan ruangan.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => $dosenBudi,
                'room_id' => $roomLabMulti,
                'date' => now()->format('Y-m-d'),
                'start_time' => '15:00:00',
                'end_time' => '17:00:00',
                'purpose' => 'Kuliah Pengganti Praktikum Web',
                'status' => 'approved',
                'admin_notes' => 'Disetujui untuk kegiatan akademik.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $mhsCitra,
                'room_id' => $roomAula,
                'date' => now()->addDays(4)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'purpose' => 'Seminar Nasional Teknologi',
                'status' => 'rejected',
                'admin_notes' => 'Ruangan aula sedang dalam masa pemeliharaan sistem tata suara dan AC.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ],
        ]);

        // 11. Reports (Kampus Aman)
        DB::table('reports')->insert([
            [
                'reporter_id' => $mhsHaikal,
                'category' => 'Pelecehan Verbal',
                'incident_date' => now()->subDays(2)->format('Y-m-d'),
                'location' => 'Kantin Gedung B',
                'involved_parties' => 'Mahasiswa Senior',
                'description' => 'Terjadi intimidasi dan kata-kata tidak pantas di area kantin saat jam istirahat siang.',
                'attachment_path' => null,
                'status' => 'investigating',
                'admin_notes' => 'Laporan sedang dalam penanganan tim Satgas PPKS untuk proses pemanggilan saksi.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDay(),
            ],
            [
                'reporter_id' => $mhsCitra,
                'category' => 'Perundungan',
                'incident_date' => now()->subDays(5)->format('Y-m-d'),
                'location' => 'Grup Diskusi Media Sosial',
                'involved_parties' => 'Akun Anonim',
                'description' => 'Penyebaran ujaran kebencian secara berulang pada forum mahasiswa.',
                'attachment_path' => null,
                'status' => 'resolved',
                'admin_notes' => 'Pelaku telah teridentifikasi, diberikan teguran keras dan konseling oleh pihak kemahasiswaan.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ],
            [
                'reporter_id' => $mhsAndi,
                'category' => 'Kehilangan / Keamanan',
                'incident_date' => now()->subDay()->format('Y-m-d'),
                'location' => 'Area Parkir Barat Gedung TI',
                'involved_parties' => 'Tidak Diketahui',
                'description' => 'Helm di motor hilang saat diparkir antara pukul 10:00 hingga 14:00.',
                'attachment_path' => null,
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);

        // 12. Aspirations (Layanan Aspirasi Fasilitas & Sarana)
        DB::table('aspirations')->insert([
            [
                'reporter_id' => $mhsHaikal,
                'category' => 'Kerusakan Fasilitas',
                'location' => 'Ruang Kuliah 2A',
                'description' => 'Proyektor ruang kuliah sering mati sendiri setelah 15 menit dan pendingin ruangan (AC) tidak dingin.',
                'attachment_path' => null,
                'status' => 'processing',
                'admin_notes' => 'Petugas sarana dan prasarana telah menjadwalkan perbaikan proyektor dan servis AC.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDay(),
            ],
            [
                'reporter_id' => $mhsAndi,
                'category' => 'Usulan Sarana',
                'location' => 'Perpustakaan Gedung TI',
                'description' => 'Mohon penambahan stop kontak / colokan listrik di meja baca mandiri dan penguatan sinyal Wi-Fi.',
                'attachment_path' => null,
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'reporter_id' => $mhsCitra,
                'category' => 'Kebersihan',
                'location' => 'Toilet Lantai 2 Gedung TI A',
                'description' => 'Kran wastafel bocor dan sabun cuci tangan kosong.',
                'attachment_path' => null,
                'status' => 'resolved',
                'admin_notes' => 'Kran air telah diperbaiki dan sabun telah diisi ulang oleh petugas kebersihan.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(2),
            ],
        ]);

        // 13. Notifications (Pusat Notifikasi Pengguna)
        DB::table('notifications')->insert([
            [
                'user_id' => $mhsHaikal,
                'title' => 'Tugas Baru: Proyek Website SAKALA',
                'message' => 'Dosen Dr. Budi Santoso telah mempublikasikan tugas baru untuk mata kuliah Pemrograman Web.',
                'type' => 'assignment_new',
                'link' => '/courses/1/assignments',
                'is_read' => false,
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
            [
                'user_id' => $mhsHaikal,
                'title' => 'Reservasi Disetujui',
                'message' => 'Pengajuan peminjaman Lab Rekayasa Komputer pada tanggal '.now()->addDays(2)->format('d/m/Y').' telah disetujui.',
                'type' => 'reservation_approved',
                'link' => '/reservations/2',
                'is_read' => false,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'user_id' => $mhsHaikal,
                'title' => 'Tindak Lanjut Aspirasi',
                'message' => 'Aspirasi Anda mengenai keluhan proyektor dan AC Ruang Kuliah 2A kini berstatus Sedang Ditindaklanjuti.',
                'type' => 'aspiration_update',
                'link' => '/aspirations/1',
                'is_read' => true,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $dosenBudi,
                'title' => 'Pengumpulan Tugas Mahasiswa',
                'message' => 'Mahasiswa M. Haikal telah mengumpulkan Tugas Pertemuan 2 (Pemrograman Web).',
                'type' => 'submission_new',
                'link' => '/courses/1/assignments',
                'is_read' => false,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Laporan Kampus Aman Baru',
                'message' => 'Terdapat laporan baru kategori "Kehilangan / Keamanan" di Area Parkir Barat Gedung TI.',
                'type' => 'report_new',
                'link' => '/admin/reports',
                'is_read' => false,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Pengajuan Aspirasi Baru',
                'message' => 'Aspirasi baru kategori "Usulan Sarana" diajukan untuk Perpustakaan Gedung TI.',
                'type' => 'aspiration_new',
                'link' => '/admin/aspirations',
                'is_read' => false,
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ],
        ]);
    }
}
