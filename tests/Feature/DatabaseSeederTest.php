<?php

namespace Tests\Feature;

use App\Models\Floor;
use App\Models\Room;
use App\Models\ScheduleException;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    public function test_database_seeder_can_be_repeated_and_links_rooms_to_floors(): void
    {
        app(DatabaseSeeder::class)->run();

        $roomCount = Room::query()->count();
        $floorCount = Floor::query()->count();
        $notificationCount = \DB::table('notifications')->count();

        $this->assertGreaterThan(0, $roomCount);
        $this->assertGreaterThan(0, $floorCount);
        $this->assertSame(0, Room::query()->whereNull('floor_id')->count());

        foreach (Room::with('floorRecord')->get() as $room) {
            $this->assertNotNull($room->floorRecord, 'Missing floor relation for room '.$room->code);
            $this->assertSame((int) $room->floor, (int) $room->floorRecord->number);
            $this->assertSame((int) $room->building_id, (int) $room->floorRecord->building_id);
        }

        $exception = ScheduleException::with('schedule')->firstOrFail();
        $this->assertSame(
            $exception->schedule->day,
            $exception->date->locale('id')->isoFormat('dddd'),
        );

        app(DatabaseSeeder::class)->run();

        $this->assertSame($roomCount, Room::query()->count());
        $this->assertSame($floorCount, Floor::query()->count());
        $this->assertSame($notificationCount, \DB::table('notifications')->count());
        $this->assertSame(1, ScheduleException::query()->count());
    }
}
