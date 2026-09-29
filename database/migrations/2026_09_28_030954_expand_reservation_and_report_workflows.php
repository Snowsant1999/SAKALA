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
            Schema::table('reservations', function (Blueprint $table) {
                $table->foreignId('course_class_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->index(['room_id', 'date', 'status', 'start_time'], 'reservations_overlap_lookup_index');
            });

            DB::statement("ALTER TABLE reservations MODIFY status VARCHAR(32) NOT NULL DEFAULT 'pending'");

            Schema::table('reports', function (Blueprint $table) {
                $table->string('priority', 16)->default('medium');
            });

            DB::statement("ALTER TABLE reports MODIFY status VARCHAR(32) NOT NULL DEFAULT 'pending'");
            DB::table('reports')->where('status', 'investigating')->update(['status' => 'in_progress']);

            Schema::create('report_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained()->cascadeOnDelete();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('from_status', 32)->nullable();
                $table->string('to_status', 32);
                $table->string('from_priority', 16)->nullable();
                $table->string('to_priority', 16)->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::dropIfExists('report_status_histories');

            DB::table('reports')->where('status', 'under_review')->update(['status' => 'investigating']);
            DB::table('reports')->where('status', 'in_progress')->update(['status' => 'investigating']);
            DB::table('reports')->where('status', 'rejected')->update(['status' => 'dismissed']);
            DB::statement("ALTER TABLE reports MODIFY status ENUM('pending', 'investigating', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending'");

            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('priority');
            });

            DB::table('reservations')->where('status', 'cancelled')->update(['status' => 'rejected']);
            DB::table('reservations')->where('status', 'completed')->update(['status' => 'approved']);
            DB::statement("ALTER TABLE reservations MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('reservations_overlap_lookup_index');
                $table->dropForeign(['course_class_id']);
                $table->dropForeign(['course_id']);
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn(['course_class_id', 'course_id', 'reviewed_by', 'notes']);
            });
    }
};
