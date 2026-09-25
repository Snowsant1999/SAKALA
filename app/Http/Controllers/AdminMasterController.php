<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class AdminMasterController extends Controller
{
    public function users()
    {
        $students = MockDataService::getMasterStudents();
        $lecturers = MockDataService::getMasterLecturers();
        return view('admin.master.users', compact('students', 'lecturers'));
    }

    public function students()
    {
        $students = MockDataService::getMasterStudents();
        return view('admin.master.students', compact('students'));
    }

    public function lecturers()
    {
        $lecturers = MockDataService::getMasterLecturers();
        return view('admin.master.lecturers', compact('lecturers'));
    }

    public function departments()
    {
        $departments = MockDataService::getMasterDepartments();
        return view('admin.master.departments', compact('departments'));
    }

    public function studyPrograms()
    {
        $programs = MockDataService::getMasterStudyPrograms();
        return view('admin.master.study_programs', compact('programs'));
    }

    public function courses()
    {
        $courses = MockDataService::getCourses();
        return view('admin.master.courses', compact('courses'));
    }

    public function classes()
    {
        $classes = [
            ['id' => 'cls-01', 'name' => 'TIM 5A', 'program' => 'Teknik Informatika Multimedia', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Dr. Budi Santoso', 'total_students' => 32],
            ['id' => 'cls-02', 'name' => 'TIM 5B', 'program' => 'Teknik Informatika Multimedia', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Siti Aminah, M.Kom', 'total_students' => 30],
            ['id' => 'cls-03', 'name' => 'TRK 5A', 'program' => 'Teknologi Rekayasa Komputer', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Ir. Hendra Wijaya, M.T', 'total_students' => 28],
            ['id' => 'cls-04', 'name' => 'TRK 5B', 'program' => 'Teknologi Rekayasa Komputer', 'academic_year' => '2026/2027 Ganjil', 'homeroom' => 'Rina Marlina, Ph.D', 'total_students' => 29],
        ];
        return view('admin.master.classes', compact('classes'));
    }

    public function buildings()
    {
        $buildings = [
            ['id' => 'bld-01', 'code' => 'TI', 'name' => 'Gedung Teknologi Informasi', 'floors_count' => 4, 'rooms_count' => 18, 'location' => 'Kampus Utama Barat'],
            ['id' => 'bld-02', 'code' => 'TE', 'name' => 'Gedung Teknik Elektro', 'floors_count' => 3, 'rooms_count' => 12, 'location' => 'Kampus Utama Timur'],
            ['id' => 'bld-03', 'code' => 'TS', 'name' => 'Gedung Teknik Sipil', 'floors_count' => 3, 'rooms_count' => 14, 'location' => 'Kampus Utama Selatan'],
        ];
        return view('admin.master.buildings', compact('buildings'));
    }

    public function rooms()
    {
        $rooms = [
            ['id' => 'rm-001', 'code' => 'TI-101', 'name' => 'Kelas 1', 'building' => 'Gedung TI', 'floor' => 'Lantai 1', 'capacity' => 30, 'type' => 'Kelas', 'status' => 'DIGUNAKAN'],
            ['id' => 'rm-002', 'code' => 'TI-102', 'name' => 'Lab Multimedia', 'building' => 'Gedung TI', 'floor' => 'Lantai 1', 'capacity' => 30, 'type' => 'Laboratorium', 'status' => 'KOSONG'],
            ['id' => 'rm-003', 'code' => 'TI-201', 'name' => 'Kelas 2A', 'building' => 'Gedung TI', 'floor' => 'Lantai 2', 'capacity' => 40, 'type' => 'Kelas', 'status' => 'DIGUNAKAN'],
            ['id' => 'rm-004', 'code' => 'TI-202', 'name' => 'Lab Rekayasa Komputer', 'building' => 'Gedung TI', 'floor' => 'Lantai 2', 'capacity' => 25, 'type' => 'Laboratorium', 'status' => 'RESERVED'],
            ['id' => 'rm-005', 'code' => 'TI-203', 'name' => 'Kelas 2B', 'building' => 'Gedung TI', 'floor' => 'Lantai 2', 'capacity' => 30, 'type' => 'Kelas', 'status' => 'KOSONG'],
            ['id' => 'rm-006', 'code' => 'TI-301', 'name' => 'Aula Gedung TI', 'building' => 'Gedung TI', 'floor' => 'Lantai 3', 'capacity' => 100, 'type' => 'Aula', 'status' => 'MAINTENANCE'],
        ];
        return view('admin.master.rooms', compact('rooms'));
    }

    public function schedules()
    {
        $schedules = MockDataService::getWeeklySchedules();
        return view('admin.master.schedules', compact('schedules'));
    }
}
