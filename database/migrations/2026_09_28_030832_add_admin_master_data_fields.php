<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('head_name')->nullable();
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->string('accreditation')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('specialization')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('status')->default('active');
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('study_program_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->string('academic_year')->nullable();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('is_maintenance')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('is_maintenance');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['study_program_id']);
            $table->dropColumn('study_program_id');
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'location']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['specialization', 'semester', 'ipk', 'status']);
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropColumn('accreditation');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('head_name');
        });
    }
};
