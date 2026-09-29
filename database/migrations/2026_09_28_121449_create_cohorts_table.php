<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->foreignId('cohort_id')->nullable()->after('lecturer_id')->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cohort_id')->nullable()->after('study_program_id')->constrained()->nullOnDelete();
        });

        foreach (DB::table('course_classes')->select('name')->distinct()->orderBy('name')->pluck('name') as $name) {
            $timestamp = now();
            DB::table('cohorts')->insertOrIgnore([
                'name' => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $cohortId = DB::table('cohorts')->where('name', $name)->value('id');

            DB::table('course_classes')->where('name', $name)->update(['cohort_id' => $cohortId]);
        }

        $memberships = DB::table('course_class_student as enrollment')
            ->join('course_classes', 'course_classes.id', '=', 'enrollment.course_class_id')
            ->join('users', 'users.id', '=', 'enrollment.user_id')
            ->where('users.role', 'mahasiswa')
            ->get([
                'users.id as user_id',
                'users.study_program_id',
                'course_classes.cohort_id',
            ]);
        $studyProgramsByCohort = [];

        foreach ($memberships as $membership) {
            DB::table('users')->where('id', $membership->user_id)->update([
                'cohort_id' => $membership->cohort_id,
            ]);

            if ($membership->study_program_id !== null) {
                $studyProgramsByCohort[$membership->cohort_id][$membership->study_program_id] = true;
            }
        }

        foreach ($studyProgramsByCohort as $cohortId => $studyProgramIds) {
            if (count($studyProgramIds) === 1) {
                DB::table('cohorts')->where('id', $cohortId)->update([
                    'study_program_id' => array_key_first($studyProgramIds),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cohort_id']);
            $table->dropColumn('cohort_id');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropForeign(['cohort_id']);
            $table->dropColumn('cohort_id');
        });

        Schema::dropIfExists('cohorts');
    }
};
