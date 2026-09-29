<?php

namespace Database\Seeders;

use App\Models\Cohort;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdditionalCourseClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $additionalClasses = [
            ['course_code' => 'TI-401', 'name' => 'TRK 5A', 'lecturer_nip' => '198001012005011002', 'program_code' => 'TRK'],
            ['course_code' => 'TI-402', 'name' => 'TRK 5A', 'lecturer_nip' => '198503152010122001', 'program_code' => 'TRK'],
            ['course_code' => 'TI-402', 'name' => 'TI 5A', 'lecturer_nip' => '198503152010122001', 'program_code' => 'IF'],
            ['course_code' => 'TI-403', 'name' => 'TI 5B', 'lecturer_nip' => '198001012005011002', 'program_code' => 'IF'],
        ];

        foreach ($additionalClasses as $additionalClass) {
            $course = Course::where('code', $additionalClass['course_code'])->firstOrFail();
            $lecturer = User::where('nim_nip', $additionalClass['lecturer_nip'])
                ->where('role', 'dosen')
                ->firstOrFail();
            $studyProgramId = StudyProgram::where('code', $additionalClass['program_code'])->value('id');
            $cohort = Cohort::firstOrCreate(
                ['name' => $additionalClass['name']],
                ['study_program_id' => $studyProgramId],
            );

            if ($cohort->study_program_id === null && $studyProgramId !== null) {
                $cohort->update(['study_program_id' => $studyProgramId]);
            }

            $class = CourseClass::firstOrCreate(
                ['course_id' => $course->id, 'name' => $additionalClass['name']],
                ['lecturer_id' => $lecturer->id, 'cohort_id' => $cohort->id],
            );

            if ($class->cohort_id !== $cohort->id) {
                $class->update(['cohort_id' => $cohort->id]);
            }
        }

        foreach (CourseClass::with('course')->get() as $class) {
            $programCode = match (strtok($class->name, ' ')) {
                'TIM', 'TI' => 'IF',
                'TRK' => 'TRK',
                default => null,
            };
            $studyProgramId = $class->course?->study_program_id
                ?? ($programCode ? StudyProgram::where('code', $programCode)->value('id') : null);
            $cohort = Cohort::firstOrCreate(
                ['name' => $class->name],
                ['study_program_id' => $studyProgramId],
            );

            if ($cohort->study_program_id === null && $studyProgramId !== null) {
                $cohort->update(['study_program_id' => $studyProgramId]);
            }

            if ($class->cohort_id !== $cohort->id) {
                $class->update(['cohort_id' => $cohort->id]);
            }
        }
    }
}
