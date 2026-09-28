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
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('label');
            $table->timestamps();
            $table->unique(['building_id', 'number']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('floor_id')->nullable()->after('building_id')->constrained()->restrictOnDelete();
        });

        $roomFloors = DB::table('rooms')
            ->select('building_id', 'floor')
            ->distinct()
            ->get();

        foreach ($roomFloors as $roomFloor) {
            $timestamp = now();
            DB::table('floors')->insertOrIgnore([
                'building_id' => $roomFloor->building_id,
                'number' => $roomFloor->floor,
                'label' => 'Lantai '.$roomFloor->floor,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $floorId = DB::table('floors')
                ->where('building_id', $roomFloor->building_id)
                ->where('number', $roomFloor->floor)
                ->value('id');

            DB::table('rooms')
                ->where('building_id', $roomFloor->building_id)
                ->where('floor', $roomFloor->floor)
                ->update(['floor_id' => $floorId]);
        }

        Schema::create('course_class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['course_class_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_class_student');

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropColumn('floor_id');
        });

        Schema::dropIfExists('floors');
    }
};
