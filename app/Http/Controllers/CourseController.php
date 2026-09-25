<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * List all courses.
     */
    public function index(Request $request)
    {
        $allCourses = MockDataService::getCourses();
        $userRole = session('user_role', 'student');
        $userEmail = session('user_email', 'student@sakala.test');

        // Filter for lecturer (only courses they teach)
        if ($userRole === 'lecturer') {
            $courses = array_filter($allCourses, fn($c) => $c['lecturer_email'] === $userEmail || $c['lecturer'] === 'Dr. Budi Santoso');
        } else {
            $courses = $allCourses;
        }

        // Search filter
        $search = $request->query('q');
        if ($search) {
            $courses = array_filter($courses, fn($c) => 
                stripos($c['name'], $search) !== false || 
                stripos($c['code'], $search) !== false ||
                stripos($c['lecturer'], $search) !== false
            );
        }

        // Semester filter
        $semester = $request->query('semester');
        if ($semester && $semester !== 'all') {
            $courses = array_filter($courses, fn($c) => (string)$c['semester'] === (string)$semester);
        }

        return view('academic.courses.index', compact('courses', 'search', 'semester'));
    }

    /**
     * Show course detail (Overview).
     */
    public function show($id)
    {
        $course = MockDataService::getCourse($id);
        if (!$course) {
            abort(404, 'Mata kuliah tidak ditemukan');
        }

        $materials = MockDataService::getMaterials($id);
        $assignments = MockDataService::getAssignments($id);

        return view('academic.courses.show', compact('course', 'materials', 'assignments'));
    }

    /**
     * Show course materials tab.
     */
    public function materials($id)
    {
        $course = MockDataService::getCourse($id);
        if (!$course) {
            abort(404, 'Mata kuliah tidak ditemukan');
        }

        $materials = MockDataService::getMaterials($id);

        return view('academic.courses.materials', compact('course', 'materials'));
    }

    /**
     * Add material (for lecturer).
     */
    public function addMaterial(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        MockDataService::addMaterial($id, [
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'file_name' => $request->input('file_name', 'Materi_Kuliah_' . date('Ymd') . '.pdf'),
            'file_size' => '2.8 MB',
        ]);

        return redirect('/courses/' . $id . '/materials')->with('success', 'Materi perkuliahan berhasil ditambahkan.');
    }

    /**
     * Delete material (for lecturer).
     */
    public function deleteMaterial($id, $materialId)
    {
        MockDataService::deleteMaterial($id, $materialId);
        return redirect('/courses/' . $id . '/materials')->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Show course assignments tab.
     */
    public function assignments($id)
    {
        $course = MockDataService::getCourse($id);
        if (!$course) {
            abort(404, 'Mata kuliah tidak ditemukan');
        }

        $assignments = MockDataService::getAssignments($id);
        $userEmail = session('user_email', 'student@sakala.test');

        return view('academic.courses.assignments', compact('course', 'assignments', 'userEmail'));
    }

    /**
     * Add assignment (for lecturer).
     */
    public function addAssignment(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required',
            'description' => 'nullable|string',
        ]);

        MockDataService::addAssignment($id, [
            'title' => $request->input('title'),
            'deadline' => $request->input('deadline'),
            'description' => $request->input('description', ''),
        ]);

        return redirect('/courses/' . $id . '/assignments')->with('success', 'Tugas baru berhasil diterbitkan.');
    }

    /**
     * Submit assignment (for student).
     */
    public function submitAssignment(Request $request, $id, $assignmentId)
    {
        $userEmail = session('user_email', 'student@sakala.test');
        $fileName = $request->input('file_name', 'Tugas_Mahasiswa_' . session('user_name', 'Andi') . '.zip');

        MockDataService::submitAssignment($id, $assignmentId, $userEmail, $fileName);

        return redirect('/courses/' . $id . '/assignments')->with('success', 'Tugas berhasil dikumpulkan tepat waktu.');
    }

    /**
     * Full weekly schedule page.
     */
    public function schedule(Request $request)
    {
        $schedules = MockDataService::getWeeklySchedules();
        $selectedDay = $request->query('day', 'Senin');

        return view('academic.schedule', compact('schedules', 'selectedDay'));
    }
}
