<?php

namespace App\Services;

class MockDataService
{
    /**
     * Get or initialize session state.
     */
    private static function getSessionState(string $key, array $default)
    {
        if (!session()->has($key)) {
            session([$key => $default]);
        }
        return session($key);
    }

    private static function setSessionState(string $key, array $data): void
    {
        session([$key => $data]);
    }

    /* =========================================================================
       1. COURSES & ACADEMIC DATA
       ========================================================================= */

    public static function getCourses()
    {
        $default = [
            [
                'id' => 'crs-001',
                'code' => 'TI-401',
                'name' => 'Pemrograman Web',
                'sks' => 3,
                'semester' => 5,
                'lecturer' => 'Dr. Budi Santoso',
                'lecturer_email' => 'lecturer@sakala.test',
                'class' => 'TIM 5A',
                'room' => 'Lab Multimedia',
                'day' => 'Senin',
                'time' => '08:00 - 10:00',
                'mode' => 'ONSITE',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknik Informatika Multimedia',
                'description' => 'Mata kuliah ini membahas arsitektur web modern, RESTful API, MVC design pattern, dan implementasi framework modern dengan Laravel & Tailwind CSS.',
                'students_count' => 32,
            ],
            [
                'id' => 'crs-002',
                'code' => 'TI-402',
                'name' => 'Basis Data Lanjut',
                'sks' => 3,
                'semester' => 5,
                'lecturer' => 'Siti Aminah, M.Kom',
                'lecturer_email' => 'siti@sakala.test',
                'class' => 'TIM 5A',
                'room' => 'Kelas 2A',
                'day' => 'Senin',
                'time' => '10:00 - 12:00',
                'mode' => 'ONSITE',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknik Informatika Multimedia',
                'description' => 'Pembahasan teknik database lanjut mencakup Transaction Management, Query Optimization, Indexing, Stored Procedures, dan NoSQL Database.',
                'students_count' => 32,
            ],
            [
                'id' => 'crs-003',
                'code' => 'TI-403',
                'name' => 'Rekayasa Perangkat Lunak',
                'sks' => 3,
                'semester' => 5,
                'lecturer' => 'Dr. Budi Santoso',
                'lecturer_email' => 'lecturer@sakala.test',
                'class' => 'TIM 5A',
                'room' => 'Daring (Zoom)',
                'day' => 'Senin',
                'time' => '13:00 - 15:00',
                'mode' => 'ONLINE',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknik Informatika Multimedia',
                'description' => 'Fokus pada siklus pengembangan perangkat lunak (SDLC), metodologi Agile/Scrum, pemodelan sistem UML, requirement engineering, dan pengujian QA.',
                'students_count' => 30,
            ],
            [
                'id' => 'crs-004',
                'code' => 'TI-404',
                'name' => 'Praktikum Jaringan Komputer',
                'sks' => 2,
                'semester' => 5,
                'lecturer' => 'Ir. Hendra Wijaya, M.T',
                'lecturer_email' => 'hendra@sakala.test',
                'class' => 'TIM 5A',
                'room' => 'Lab Rekayasa Komputer',
                'day' => 'Selasa',
                'time' => '08:00 - 11:00',
                'mode' => 'ONSITE',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Komputer',
                'description' => 'Praktik instalasi dan konfigurasi topologi jaringan komputer, routing protokol, subnetting VLSM, VLAN, VPN, dan pengamanan server.',
                'students_count' => 28,
            ],
            [
                'id' => 'crs-005',
                'code' => 'TI-405',
                'name' => 'Kecerdasan Buatan',
                'sks' => 3,
                'semester' => 5,
                'lecturer' => 'Rina Marlina, Ph.D',
                'lecturer_email' => 'rina@sakala.test',
                'class' => 'TIM 5A',
                'room' => 'Kelas 2B',
                'day' => 'Rabu',
                'time' => '10:00 - 12:00',
                'mode' => 'ONSITE',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknik Informatika Multimedia',
                'description' => 'Konsep dasar Artificial Intelligence, Supervised & Unsupervised Learning, Decision Tree, Neural Networks, dan Computer Vision dasar.',
                'students_count' => 32,
            ],
        ];

        return self::getSessionState('courses_data', $default);
    }

    public static function getCourse(string $id)
    {
        $courses = self::getCourses();
        return collect($courses)->firstWhere('id', $id);
    }

    /* =========================================================================
       2. MATERIALS (Materi Perkuliahan)
       ========================================================================= */

    public static function getMaterials(string $courseId)
    {
        $default = [
            'crs-001' => [
                [
                    'id' => 'mat-101',
                    'title' => 'Pertemuan 1 - Arsitektur Web Modern & Protokol HTTP',
                    'description' => 'Membahas konsep client-server, status code HTTP, REST architectural constraints, dan pengenalan framework MVC.',
                    'file_name' => '01_Intro_Web_MVC_Architecture.pdf',
                    'file_size' => '2.4 MB',
                    'uploaded_at' => '2026-09-08 09:30',
                ],
                [
                    'id' => 'mat-102',
                    'title' => 'Pertemuan 2 - Routing, Controller & Middleware Laravel',
                    'description' => 'Panduan routing di Laravel, pembuatan Controller berbasis Resource, penanganan Request & Response, dan custom Middleware.',
                    'file_name' => '02_Laravel_Routing_and_Controller.pdf',
                    'file_size' => '3.8 MB',
                    'uploaded_at' => '2026-09-15 10:15',
                ],
                [
                    'id' => 'mat-103',
                    'title' => 'Pertemuan 3 - Blade Templating & Tailwind CSS v4',
                    'description' => 'Teknik modularisasi tampilan dengan Blade Layouts, component directives, serta styling cepat memanfaatkan Tailwind CSS.',
                    'file_name' => '03_Blade_Layouts_Tailwind.pdf',
                    'file_size' => '4.1 MB',
                    'uploaded_at' => '2026-09-22 08:45',
                ],
            ],
            'crs-002' => [
                [
                    'id' => 'mat-201',
                    'title' => 'Pertemuan 1 - Konsep ACID & Transaction Management',
                    'description' => 'Materi pendalaman Atomicity, Consistency, Isolation, Durability dalam database transaksi tinggi.',
                    'file_name' => '01_Database_Transactions.pdf',
                    'file_size' => '1.9 MB',
                    'uploaded_at' => '2026-09-08 11:00',
                ],
                [
                    'id' => 'mat-202',
                    'title' => 'Pertemuan 2 - Indexing B-Tree & Query Optimization',
                    'description' => 'Teknik optimasi query kompleks dengan EXPLAIN query plan, clustering index, dan composite index.',
                    'file_name' => '02_Query_Optimization_Index.pdf',
                    'file_size' => '2.7 MB',
                    'uploaded_at' => '2026-09-15 11:30',
                ],
            ],
            'crs-003' => [
                [
                    'id' => 'mat-301',
                    'title' => 'Pertemuan 1 - Software Requirements Specification (SRS)',
                    'description' => 'Standar penyusunan dokumen SRS IEEE 830, Functional & Non-Functional Requirements, dan Use Case Diagram.',
                    'file_name' => '01_SRS_IEEE830_Standard.pdf',
                    'file_size' => '3.2 MB',
                    'uploaded_at' => '2026-09-08 14:00',
                ],
            ],
            'crs-004' => [
                [
                    'id' => 'mat-401',
                    'title' => 'Modul 1 - VLAN & Inter-VLAN Routing',
                    'description' => 'Konfigurasi VLAN ID, Trunking IEEE 802.1Q, dan Router-on-a-Stick pada switch managed.',
                    'file_name' => 'Modul_01_VLAN_Trunking.pdf',
                    'file_size' => '5.0 MB',
                    'uploaded_at' => '2026-09-09 08:00',
                ],
            ],
            'crs-005' => [
                [
                    'id' => 'mat-501',
                    'title' => 'Pertemuan 1 - Pengantar AI & Machine Learning',
                    'description' => 'Taksonomi Artificial Intelligence, Supervised vs Unsupervised Learning, dan Data Preprocessing.',
                    'file_name' => '01_Intro_Machine_Learning.pdf',
                    'file_size' => '2.1 MB',
                    'uploaded_at' => '2026-09-10 10:00',
                ],
            ],
        ];

        $all = self::getSessionState('materials_data', $default);
        return $all[$courseId] ?? [];
    }

    public static function addMaterial(string $courseId, array $materialData): void
    {
        $all = self::getSessionState('materials_data', []);
        if (!isset($all[$courseId])) {
            $all[$courseId] = self::getMaterials($courseId);
        }

        $newMaterial = [
            'id' => 'mat-' . uniqid(),
            'title' => $materialData['title'],
            'description' => $materialData['description'] ?? '',
            'file_name' => $materialData['file_name'] ?? 'Materi_' . date('Ymd_His') . '.pdf',
            'file_size' => $materialData['file_size'] ?? '1.5 MB',
            'uploaded_at' => date('Y-m-d H:i'),
        ];

        array_unshift($all[$courseId], $newMaterial);
        self::setSessionState('materials_data', $all);
    }

    public static function deleteMaterial(string $courseId, string $materialId): void
    {
        $all = self::getSessionState('materials_data', []);
        if (isset($all[$courseId])) {
            $all[$courseId] = array_values(array_filter($all[$courseId], fn($m) => $m['id'] !== $materialId));
            self::setSessionState('materials_data', $all);
        }
    }

    /* =========================================================================
       3. ASSIGNMENTS (Tugas & Pengumpulan)
       ========================================================================= */

    public static function getAssignments(string $courseId)
    {
        $default = [
            'crs-001' => [
                [
                    'id' => 'asg-101',
                    'title' => 'Tugas 1: Implementasi REST API Laravel',
                    'description' => 'Buatlah RESTful API CRUD lengkap untuk entitas Produk dan Kategori dengan validasi request dan response format JSON yang terstandar.',
                    'deadline' => '2026-09-25 23:59',
                    'deadline_formatted' => 'Jumat, 25 Sep 2026, 23:59 WITA',
                    'submissions' => [
                        'student@sakala.test' => [
                            'status' => 'SUBMITTED',
                            'file_name' => 'Tugas1_REST_API_AndiPratama.zip',
                            'submitted_at' => '2026-09-23 16:40',
                            'grade' => '90/100',
                            'feedback' => 'Implementasi resource controller dan JSON response sangat rapi.',
                        ]
                    ],
                ],
                [
                    'id' => 'asg-102',
                    'title' => 'Tugas 2: Desain Antarmuka Frontend Blade & Tailwind',
                    'description' => 'Rancang antarmuka dashboard responsif dengan komponen modular Blade dan skema warna Navy/Indigo.',
                    'deadline' => '2026-10-02 23:59',
                    'deadline_formatted' => 'Jumat, 02 Okt 2026, 23:59 WITA',
                    'submissions' => [],
                ]
            ],
            'crs-002' => [
                [
                    'id' => 'asg-201',
                    'title' => 'Tugas 1: ER Diagram & Normalisasi Sistem Rumah Sakit',
                    'description' => 'Rancang ERD lengkap bentuk normal ketiga (3NF) untuk modul rawat inap dan billing rumah sakit.',
                    'deadline' => '2026-09-27 23:59',
                    'deadline_formatted' => 'Minggu, 27 Sep 2026, 23:59 WITA',
                    'submissions' => [],
                ],
            ],
            'crs-003' => [
                [
                    'id' => 'asg-301',
                    'title' => 'Tugas 1: Dokumen Software Requirement Specification (SRS)',
                    'description' => 'Susun dokumen spesifikasi kebutuhan perangkat lunak (SRS) berbasis IEEE 830 untuk proyek kelompok akhir semester.',
                    'deadline' => '2026-10-01 23:59',
                    'deadline_formatted' => 'Kamis, 01 Okt 2026, 23:59 WITA',
                    'submissions' => [],
                ],
            ],
            'crs-004' => [
                [
                    'id' => 'asg-401',
                    'title' => 'Laporan Praktikum 1: Konfigurasi VLAN Trunking',
                    'description' => 'Upload laporan resmi praktikum format PDF disertai screenshot konfigurasi CLI Cisco Packet Tracer.',
                    'deadline' => '2026-09-28 23:59',
                    'deadline_formatted' => 'Senin, 28 Sep 2026, 23:59 WITA',
                    'submissions' => [],
                ],
            ],
            'crs-005' => [
                [
                    'id' => 'asg-501',
                    'title' => 'Tugas Mandiri: Analisis Dataset Prediksi Kanker',
                    'description' => 'Lakukan exploratory data analysis (EDA) dan preprocessing pada dataset kanker payudara Wisconsin.',
                    'deadline' => '2026-10-05 23:59',
                    'deadline_formatted' => 'Senin, 05 Okt 2026, 23:59 WITA',
                    'submissions' => [],
                ],
            ],
        ];

        $all = self::getSessionState('assignments_data', $default);
        return $all[$courseId] ?? [];
    }

    public static function addAssignment(string $courseId, array $data): void
    {
        $all = self::getSessionState('assignments_data', []);
        if (!isset($all[$courseId])) {
            $all[$courseId] = self::getAssignments($courseId);
        }

        $newAssignment = [
            'id' => 'asg-' . uniqid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'deadline' => $data['deadline'],
            'deadline_formatted' => date('d M Y, H:i', strtotime($data['deadline'])) . ' WITA',
            'submissions' => [],
        ];

        array_unshift($all[$courseId], $newAssignment);
        self::setSessionState('assignments_data', $all);
    }

    public static function submitAssignment(string $courseId, string $assignmentId, string $userEmail, string $fileName): void
    {
        $all = self::getSessionState('assignments_data', []);
        if (!isset($all[$courseId])) {
            $all[$courseId] = self::getAssignments($courseId);
        }

        foreach ($all[$courseId] as &$assignment) {
            if ($assignment['id'] === $assignmentId) {
                $assignment['submissions'][$userEmail] = [
                    'status' => 'SUBMITTED',
                    'file_name' => $fileName,
                    'submitted_at' => date('Y-m-d H:i'),
                    'grade' => 'Menunggu Penilaian',
                    'feedback' => null,
                ];
                break;
            }
        }

        self::setSessionState('assignments_data', $all);
    }

    /* =========================================================================
       4. SCHEDULES (Jadwal Kuliah Mingguan)
       ========================================================================= */

    public static function getWeeklySchedules()
    {
        return [
            'Senin' => [
                ['time' => '08:00 - 10:00', 'code' => 'TI-401', 'course' => 'Pemrograman Web', 'lecturer' => 'Dr. Budi Santoso', 'room' => 'Lab Multimedia', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'indigo'],
                ['time' => '10:00 - 12:00', 'code' => 'TI-402', 'course' => 'Basis Data Lanjut', 'lecturer' => 'Siti Aminah, M.Kom', 'room' => 'Kelas 2A', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'blue'],
                ['time' => '13:00 - 15:00', 'code' => 'TI-403', 'course' => 'Rekayasa Perangkat Lunak', 'lecturer' => 'Dr. Budi Santoso', 'room' => 'Daring (Zoom)', 'building' => '—', 'class' => 'TIM 5A', 'mode' => 'ONLINE', 'color' => 'purple'],
            ],
            'Selasa' => [
                ['time' => '08:00 - 11:00', 'code' => 'TI-404', 'course' => 'Praktikum Jaringan Komputer', 'lecturer' => 'Ir. Hendra Wijaya, M.T', 'room' => 'Lab Rekayasa Komputer', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'emerald'],
                ['time' => '13:00 - 15:00', 'code' => 'TI-406', 'course' => 'Etika Profesi TI', 'lecturer' => 'Dra. Wahyuni, M.Pd', 'room' => 'Kelas 1', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'amber'],
            ],
            'Rabu' => [
                ['time' => '10:00 - 12:00', 'code' => 'TI-405', 'course' => 'Kecerdasan Buatan', 'lecturer' => 'Rina Marlina, Ph.D', 'room' => 'Kelas 2B', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'cyan'],
                ['time' => '13:00 - 15:00', 'code' => 'TI-407', 'course' => 'Interaksi Manusia & Komputer', 'lecturer' => 'Ahmad Fauzi, M.Kom', 'room' => 'Lab Multimedia', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'rose'],
            ],
            'Kamis' => [
                ['time' => '08:00 - 10:00', 'code' => 'TI-408', 'course' => 'Keamanan Sistem Komputer', 'lecturer' => 'Ir. Hendra Wijaya, M.T', 'room' => 'Lab Rekayasa Komputer', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'indigo'],
                ['time' => '10:00 - 12:00', 'code' => 'TI-409', 'course' => 'Bahasa Inggris Teknis II', 'lecturer' => 'Grace Natalia, M.Hum', 'room' => 'Kelas 2A', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'teal'],
            ],
            'Jumat' => [
                ['time' => '08:30 - 11:00', 'code' => 'TI-410', 'course' => 'Kapita Selekta TI', 'lecturer' => 'Dr. Budi Santoso', 'room' => 'Aula Gedung TI', 'building' => 'Gedung TI', 'class' => 'TIM 5A', 'mode' => 'ONSITE', 'color' => 'slate'],
            ],
        ];
    }

    /* =========================================================================
       5. RESERVATIONS & CONFLICT MANAGEMENT (PRD Scenario C)
       ========================================================================= */

    public static function getReservations()
    {
        $default = [
            [
                'id' => 'rsv-001',
                'requester_name' => 'Andi Pratama',
                'requester_email' => 'student@sakala.test',
                'requester_role' => 'Mahasiswa',
                'requester_nim' => '220102030',
                'class' => 'Teknik Komputer 5A',
                'study_program' => 'Teknologi Rekayasa Komputer',
                'department' => 'Teknologi Informasi',
                'course' => 'Praktikum Jaringan',
                'lecturer' => 'Ir. Hendra Wijaya, M.T',
                'building' => 'Gedung Teknologi Informasi',
                'floor' => 'Lantai 2',
                'room_id' => 'rm-004',
                'room_name' => 'Lab Rekayasa Komputer',
                'date' => '2026-09-26',
                'start_time' => '13:00',
                'end_time' => '15:00',
                'time_formatted' => '13:00 - 15:00',
                'purpose' => 'Praktikum Tambahan Konfigurasi Router & Switch Cisco',
                'notes' => 'Diperlukan untuk persiapan ujian sertifikasi jaringan.',
                'status' => 'PENDING',
                'created_at' => '2026-09-24 10:15',
                'admin_note' => null,
            ],
            [
                'id' => 'rsv-002',
                'requester_name' => 'Citra Dewi',
                'requester_email' => 'citra@sakala.test',
                'requester_role' => 'Mahasiswa',
                'requester_nim' => '220102045',
                'class' => 'TIM 5A',
                'study_program' => 'Teknik Informatika Multimedia',
                'department' => 'Teknologi Informasi',
                'course' => 'Rekayasa Perangkat Lunak',
                'lecturer' => 'Dr. Budi Santoso',
                'building' => 'Gedung Teknologi Informasi',
                'floor' => 'Lantai 2',
                'room_id' => 'rm-004',
                'room_name' => 'Lab Rekayasa Komputer',
                'date' => '2026-09-26',
                'start_time' => '13:00',
                'end_time' => '15:00',
                'time_formatted' => '13:00 - 15:00',
                'purpose' => 'Uji Coba Deployment Server & Testing Proyek Akhir',
                'notes' => 'Menggunakan server lokal di laboratorium.',
                'status' => 'PENDING',
                'created_at' => '2026-09-24 11:30',
                'admin_note' => null,
            ],
            [
                'id' => 'rsv-003',
                'requester_name' => 'Dr. Budi Santoso',
                'requester_email' => 'lecturer@sakala.test',
                'requester_role' => 'Dosen',
                'requester_nim' => '0012058001',
                'class' => 'TIM 5B',
                'study_program' => 'Teknik Informatika Multimedia',
                'department' => 'Teknologi Informasi',
                'course' => 'Basis Data Lanjut',
                'lecturer' => 'Dr. Budi Santoso',
                'building' => 'Gedung Teknologi Informasi',
                'floor' => 'Lantai 2',
                'room_id' => 'rm-005',
                'room_name' => 'Kelas 2B',
                'date' => '2026-09-27',
                'start_time' => '08:00',
                'end_time' => '10:00',
                'time_formatted' => '08:00 - 10:00',
                'purpose' => 'Kuliah Pengganti Pertemuan 3 - Query Optimization',
                'notes' => 'Mengejar materi sebelum UTS.',
                'status' => 'PENDING',
                'created_at' => '2026-09-24 14:00',
                'admin_note' => null,
            ],
            [
                'id' => 'rsv-004',
                'requester_name' => 'Andi Pratama',
                'requester_email' => 'student@sakala.test',
                'requester_role' => 'Mahasiswa',
                'requester_nim' => '220102030',
                'class' => 'TIM 5A',
                'study_program' => 'Teknik Informatika Multimedia',
                'department' => 'Teknologi Informasi',
                'course' => 'Pemrograman Web',
                'lecturer' => 'Dr. Budi Santoso',
                'building' => 'Gedung Teknologi Informasi',
                'floor' => 'Lantai 1',
                'room_id' => 'rm-002',
                'room_name' => 'Lab Multimedia',
                'date' => '2026-09-20',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'time_formatted' => '10:00 - 12:00',
                'purpose' => 'Workshop UI/UX Design & Frontend Himpunan Mahasiswa',
                'notes' => 'Telah berkoordinasi dengan Ketua Jurusan.',
                'status' => 'APPROVED',
                'created_at' => '2026-09-18 09:00',
                'admin_note' => 'Disetujui untuk kegiatan resmi himpunan mahasiswa.',
            ],
            [
                'id' => 'rsv-005',
                'requester_name' => 'Eka Putra',
                'requester_email' => 'eka@sakala.test',
                'requester_role' => 'Mahasiswa',
                'requester_nim' => '220102060',
                'class' => 'TIM 5A',
                'study_program' => 'Teknik Informatika Multimedia',
                'department' => 'Teknologi Informasi',
                'course' => 'Kecerdasan Buatan',
                'lecturer' => 'Rina Marlina, Ph.D',
                'building' => 'Gedung Teknologi Informasi',
                'floor' => 'Lantai 1',
                'room_id' => 'rm-001',
                'room_name' => 'Kelas 1',
                'date' => '2026-09-19',
                'start_time' => '15:00',
                'end_time' => '17:00',
                'time_formatted' => '15:00 - 17:00',
                'purpose' => 'Belajar Kelompok Mandiri',
                'notes' => null,
                'status' => 'REJECTED',
                'created_at' => '2026-09-18 13:20',
                'admin_note' => 'Ruangan telah dijadwalkan untuk pemeliharaan rutin proyektor dan AC.',
            ],
        ];

        return self::getSessionState('reservations_data', $default);
    }

    public static function createReservation(array $data)
    {
        $reservations = self::getReservations();

        $newReservation = [
            'id' => 'rsv-' . str_pad(count($reservations) + 1, 3, '0', STR_PAD_LEFT),
            'requester_name' => session('user_name', 'Andi Pratama'),
            'requester_email' => session('user_email', 'student@sakala.test'),
            'requester_role' => session('user_role') === 'lecturer' ? 'Dosen' : 'Mahasiswa',
            'requester_nim' => session('user_role') === 'lecturer' ? '0012058001' : '220102030',
            'class' => $data['class'] ?? 'TIM 5A',
            'study_program' => 'Teknik Informatika Multimedia',
            'department' => 'Teknologi Informasi',
            'course' => $data['course'] ?? 'Kegiatan Akademik',
            'lecturer' => $data['lecturer'] ?? 'Dr. Budi Santoso',
            'building' => $data['building'] ?? 'Gedung Teknologi Informasi',
            'floor' => $data['floor'] ?? 'Lantai 2',
            'room_id' => $data['room_id'] ?? 'rm-004',
            'room_name' => $data['room_name'] ?? 'Lab Rekayasa Komputer',
            'date' => $data['date'] ?? date('Y-m-d', strtotime('+1 day')),
            'start_time' => $data['start_time'] ?? '13:00',
            'end_time' => $data['end_time'] ?? '15:00',
            'time_formatted' => ($data['start_time'] ?? '13:00') . ' - ' . ($data['end_time'] ?? '15:00'),
            'purpose' => $data['purpose'] ?? 'Kegiatan perkuliahan / praktikum',
            'notes' => $data['notes'] ?? '',
            'status' => 'PENDING',
            'created_at' => date('Y-m-d H:i'),
            'admin_note' => null,
        ];

        array_unshift($reservations, $newReservation);
        self::setSessionState('reservations_data', $reservations);

        return $newReservation;
    }

    /**
     * Approve reservation & auto-resolve conflicts (PRD Scenario C).
     */
    public static function approveReservation(string $id, ?string $adminNote = null)
    {
        $reservations = self::getReservations();
        $target = null;

        // Find target reservation
        foreach ($reservations as $r) {
            if ($r['id'] === $id) {
                $target = $r;
                break;
            }
        }

        if (!$target) return false;

        // Approve target and automatically reject any overlapping PENDING requests
        foreach ($reservations as &$r) {
            if ($r['id'] === $id) {
                $r['status'] = 'APPROVED';
                $r['admin_note'] = $adminNote ?? 'Disetujui oleh Admin Akademik.';
            } elseif (
                $r['room_id'] === $target['room_id'] &&
                $r['date'] === $target['date'] &&
                $r['status'] === 'PENDING' &&
                $r['start_time'] === $target['start_time']
            ) {
                // Auto-reject conflicting reservation
                $r['status'] = 'REJECTED';
                $r['admin_note'] = 'Ditolak otomatis oleh sistem: Slot waktu dan ruangan telah disetujui untuk pengajuan (' . $target['requester_name'] . ' - ' . $target['class'] . ').';
            }
        }

        self::setSessionState('reservations_data', $reservations);
        return true;
    }

    public static function rejectReservation(string $id, ?string $adminNote = null)
    {
        $reservations = self::getReservations();

        foreach ($reservations as &$r) {
            if ($r['id'] === $id) {
                $r['status'] = 'REJECTED';
                $r['admin_note'] = $adminNote ?? 'Ditolak oleh Admin.';
                break;
            }
        }

        self::setSessionState('reservations_data', $reservations);
        return true;
    }

    public static function getConflicts()
    {
        $reservations = self::getReservations();
        $groups = [];

        foreach ($reservations as $r) {
            if ($r['status'] === 'PENDING') {
                $key = $r['room_id'] . '_' . $r['date'] . '_' . $r['start_time'];
                $groups[$key][] = $r;
            }
        }

        // Only return groups with 2+ requests
        return array_filter($groups, fn($g) => count($g) > 1);
    }

    /* =========================================================================
       6. KAMPUS AMAN & REPORTS (PRD Section 13)
       ========================================================================= */

    public static function getReports()
    {
        $default = [
            [
                'id' => 'rpt-001',
                'reporter_email' => 'student@sakala.test',
                'reporter_name' => 'Andi Pratama',
                'reporter_role' => 'Mahasiswa',
                'reporter_nim' => '220102030',
                'category' => 'Intimidasi',
                'incident_date' => '2026-09-22',
                'location' => 'Gedung TI Lantai 2 Koridor Barat',
                'description' => 'Terjadi pemaksaan dan intimidasi terkait kepanitiaan oleh oknum senior pada sore hari setelah jam perkuliahan selesai.',
                'involved_parties' => 'Oknum panitia senior angkatan atas',
                'priority' => 'HIGH',
                'status' => 'SUBMITTED',
                'attachments' => 'bukti_chat_intimidasi.png',
                'admin_note' => 'Laporan telah diterima satgas dan sedang menunggu jadwal verifikasi awal.',
                'created_at' => '2026-09-22 18:30',
                'timeline' => [
                    ['title' => 'Laporan Dibuat', 'time' => '22 Sep 2026, 18:30', 'desc' => 'Laporan berhasil dibuat dan masuk antrean sistem secara rahasia.'],
                ],
            ],
            [
                'id' => 'rpt-002',
                'reporter_email' => 'student@sakala.test',
                'reporter_name' => 'Andi Pratama',
                'reporter_role' => 'Mahasiswa',
                'reporter_nim' => '220102030',
                'category' => 'Perundungan',
                'incident_date' => '2026-09-15',
                'location' => 'Kantin Gedung TI Utama',
                'description' => 'Tindakan pelecehan verbal dan pengucilan secara terus menerus saat berada di area publik kantin kampus.',
                'involved_parties' => 'Mahasiswa Kelas X',
                'priority' => 'MEDIUM',
                'status' => 'UNDER_REVIEW',
                'attachments' => 'rekaman_suara_kejadian.m4a',
                'admin_note' => 'Sedang dijadwalkan sesi konseling dan klarifikasi tertutup oleh tim penanganan kampus aman.',
                'created_at' => '2026-09-15 14:15',
                'timeline' => [
                    ['title' => 'Laporan Dibuat', 'time' => '15 Sep 2026, 14:15', 'desc' => 'Laporan terdaftar.'],
                    ['title' => 'Dalam Peninjauan (Under Review)', 'time' => '16 Sep 2026, 09:00', 'desc' => 'Satgas melakukan telaah bukti dan menentukan prioritas penanganan MEDIUM.'],
                ],
            ],
            [
                'id' => 'rpt-003',
                'reporter_email' => 'lecturer@sakala.test',
                'reporter_name' => 'Dr. Budi Santoso',
                'reporter_role' => 'Dosen',
                'reporter_nim' => '0012058001',
                'category' => 'Pelecehan Verbal',
                'incident_date' => '2026-09-10',
                'location' => 'Ruang Dosen Gedung TI Lantai 3',
                'description' => 'Pesan teks berulang yang tidak pantas dan bernada ancaman yang dikirimkan melalui nomor anonim.',
                'involved_parties' => 'Nomor WhatsApp Anonim',
                'priority' => 'LOW',
                'status' => 'IN_PROGRESS',
                'attachments' => 'tangkapan_layar_chat.pdf',
                'admin_note' => 'Koordinasi dengan tim teknis TI untuk investigasi jejak digital pesan.',
                'created_at' => '2026-09-10 11:20',
                'timeline' => [
                    ['title' => 'Laporan Dibuat', 'time' => '10 Sep 2026, 11:20', 'desc' => 'Laporan terdaftar.'],
                    ['title' => 'Ditelaah Satgas', 'time' => '11 Sep 2026, 08:30', 'desc' => 'Bukti chat diverifikasi.'],
                    ['title' => 'Investigasi Berjalan (In Progress)', 'time' => '12 Sep 2026, 13:00', 'desc' => 'Proses investigasi oleh tim terkait sedang berlangsung.'],
                ],
            ],
        ];

        return self::getSessionState('reports_data', $default);
    }

    public static function createReport(array $data)
    {
        $reports = self::getReports();

        $newReport = [
            'id' => 'rpt-' . str_pad(count($reports) + 1, 3, '0', STR_PAD_LEFT),
            'reporter_email' => session('user_email', 'student@sakala.test'),
            'reporter_name' => session('user_name', 'Andi Pratama'),
            'reporter_role' => session('user_role') === 'lecturer' ? 'Dosen' : 'Mahasiswa',
            'reporter_nim' => session('user_role') === 'lecturer' ? '0012058001' : '220102030',
            'category' => $data['category'] ?? 'Lainnya',
            'incident_date' => $data['incident_date'] ?? date('Y-m-d'),
            'location' => $data['location'] ?? 'Lingkungan Kampus',
            'description' => $data['description'] ?? '',
            'involved_parties' => $data['involved_parties'] ?? 'Dirahasiakan',
            'priority' => 'HIGH',
            'status' => 'SUBMITTED',
            'attachments' => $data['attachments'] ?? 'lampiran_bukti.pdf',
            'admin_note' => 'Laporan Anda telah berhasil masuk dan dijaga kerahasiaannya.',
            'created_at' => date('Y-m-d H:i'),
            'timeline' => [
                ['title' => 'Laporan Dibuat', 'time' => date('d M Y, H:i'), 'desc' => 'Laporan baru telah dibuat secara rahasia dan aman.'],
            ],
        ];

        array_unshift($reports, $newReport);
        self::setSessionState('reports_data', $reports);

        return $newReport;
    }

    public static function updateReport(string $id, string $status, string $priority, ?string $adminNote = null)
    {
        $reports = self::getReports();

        foreach ($reports as &$r) {
            if ($r['id'] === $id) {
                $r['status'] = $status;
                $r['priority'] = $priority;
                if ($adminNote) {
                    $r['admin_note'] = $adminNote;
                }

                $r['timeline'][] = [
                    'title' => 'Status Diperbarui: ' . $status,
                    'time' => date('d M Y, H:i'),
                    'desc' => $adminNote ?? 'Status laporan dan prioritas telah diperbarui oleh Admin Satgas Kampus Aman.',
                ];
                break;
            }
        }

        self::setSessionState('reports_data', $reports);
        return true;
    }

    /* =========================================================================
       7. MASTER DATA ADMIN
       ========================================================================= */

    public static function getMasterStudents()
    {
        return [
            ['id' => 'std-001', 'nim' => '220102030', 'name' => 'Andi Pratama', 'email' => 'student@sakala.test', 'program' => 'Teknik Informatika Multimedia', 'class' => 'TIM 5A', 'status' => 'Aktif'],
            ['id' => 'std-002', 'nim' => '220102045', 'name' => 'Citra Dewi', 'email' => 'citra@sakala.test', 'program' => 'Teknik Informatika Multimedia', 'class' => 'TIM 5A', 'status' => 'Aktif'],
            ['id' => 'std-003', 'nim' => '220102060', 'name' => 'Eka Putra', 'email' => 'eka@sakala.test', 'program' => 'Teknologi Rekayasa Komputer', 'class' => 'TRK 5A', 'status' => 'Aktif'],
            ['id' => 'std-004', 'nim' => '220102012', 'name' => 'Rina Salsabila', 'email' => 'rina.s@sakala.test', 'program' => 'Teknik Informatika Multimedia', 'class' => 'TIM 5B', 'status' => 'Aktif'],
            ['id' => 'std-005', 'nim' => '220102078', 'name' => 'Fajar Nugraha', 'email' => 'fajar@sakala.test', 'program' => 'Teknologi Rekayasa Komputer', 'class' => 'TRK 5B', 'status' => 'Cuti'],
        ];
    }

    public static function getMasterLecturers()
    {
        return [
            ['id' => 'lec-001', 'nidn' => '0012058001', 'name' => 'Dr. Budi Santoso', 'email' => 'lecturer@sakala.test', 'department' => 'Teknologi Informasi', 'specialization' => 'Web Architecture & Software Engineering', 'status' => 'Aktif'],
            ['id' => 'lec-002', 'nidn' => '0015088202', 'name' => 'Siti Aminah, M.Kom', 'email' => 'siti@sakala.test', 'department' => 'Teknologi Informasi', 'specialization' => 'Database Systems & Big Data', 'status' => 'Aktif'],
            ['id' => 'lec-003', 'nidn' => '0020017903', 'name' => 'Ir. Hendra Wijaya, M.T', 'email' => 'hendra@sakala.test', 'department' => 'Teknologi Informasi', 'specialization' => 'Computer Networks & Cybersecurity', 'status' => 'Aktif'],
            ['id' => 'lec-004', 'nidn' => '0005128504', 'name' => 'Rina Marlina, Ph.D', 'email' => 'rina@sakala.test', 'department' => 'Teknologi Informasi', 'specialization' => 'Artificial Intelligence & Machine Learning', 'status' => 'Aktif'],
        ];
    }

    public static function getMasterDepartments()
    {
        return [
            ['id' => 'dpt-01', 'code' => 'TI', 'name' => 'Teknologi Informasi', 'head' => 'Dr. Budi Santoso', 'programs_count' => 3, 'students_count' => 450],
            ['id' => 'dpt-02', 'code' => 'TE', 'name' => 'Teknik Elektro', 'head' => 'Ir. Hendra Wijaya, M.T', 'programs_count' => 2, 'students_count' => 380],
            ['id' => 'dpt-03', 'code' => 'TS', 'name' => 'Teknik Sipil', 'head' => 'Dr. Ir. Wahyudi, M.T', 'programs_count' => 2, 'students_count' => 320],
        ];
    }

    public static function getMasterStudyPrograms()
    {
        return [
            ['id' => 'prd-01', 'code' => 'TIM', 'name' => 'Teknik Informatika Multimedia', 'department' => 'Teknologi Informasi', 'degree' => 'D4 / Sarjana Terapan', 'accreditation' => 'Unggul'],
            ['id' => 'prd-02', 'code' => 'TRK', 'name' => 'Teknologi Rekayasa Komputer', 'department' => 'Teknologi Informasi', 'degree' => 'D4 / Sarjana Terapan', 'accreditation' => 'Baik Sekali'],
            ['id' => 'prd-03', 'code' => 'TRPL', 'name' => 'Teknologi Rekayasa Perangkat Lunak', 'department' => 'Teknologi Informasi', 'degree' => 'D4 / Sarjana Terapan', 'accreditation' => 'Unggul'],
            ['id' => 'prd-04', 'code' => 'TL', 'name' => 'Teknik Listrik', 'department' => 'Teknik Elektro', 'degree' => 'D3 / Ahli Madya', 'accreditation' => 'A'],
        ];
    }
}
