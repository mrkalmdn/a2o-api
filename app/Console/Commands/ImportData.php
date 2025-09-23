<?php

namespace App\Console\Commands;

use App\Models\EventName;
use App\Models\LogEvent;
use App\Models\Market;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\select;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import local csv data to database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $file = select(
            label: 'What file do you want to import?',
            options: ['Markets', 'Event Names', 'Log Events', 'Log Service Titan Jobs']
        );

        $path = match ($file) {
            'Markets' => storage_path('app/private/importables/markets.csv'),
            'Event Names' => storage_path('app/private/importables/event_names.csv'),
            'Log Events' => storage_path('app/private/importables/log_events.csv'),
            'Log Service Titan Jobs' => storage_path('app/private/importables/log_service_titan_jobs.csv'),
        };

        $this->info("Importing $file...");

        $now = now()->format('Y-m-d H:i:s');

        $importable = str($file)->camel()->toString();

        $this->$importable($path, $now);

        $this->info("$file imported successfully!");
    }

    private function markets(string $path, string $now): void
    {
        collect(file($path))
            ->skip(1)
            ->map(fn ($line) => str_getcsv($line))
            ->map(fn ($row) => [
                'id' => $row[0],
                'name' => $row[1],
                'domain' => $row[2],
                'path' => $row[3],
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->chunk(1000)
            ->each(fn ($chunk) => Market::insert($chunk->toArray()));
    }

    private function eventNames(string $path, string $now): void
    {
        collect(file($path))
            ->skip(1)
            ->map(fn ($line) => str_getcsv($line))
            ->map(fn ($row) => [
                'id' => $row[0],
                'name' => $row[1],
                'display_on_client' => $row[2],
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->chunk(1000)
            ->each(fn ($chunk) => EventName::insert($chunk->toArray()));
    }

    private function logEvents(string $path, string $now): void
    {
        $processes = 10;

        $tasks = [];
        for ($i = 0; $i < $processes; $i++) {
            $tasks[] = function () use ($path, $now, $i, $processes) {
                DB::reconnect();

                $handle = fopen($path, 'r');
                fgets($handle); // Skip header
                $current = 0;
                $events = [];

                while (($line = fgets($handle)) !== false) {
                    if ($current++ % $processes !== $i) {
                        continue;
                    }

                    $row = str_getcsv($line);
                    $events[] = [
                        'id' => $row[0],
                        'market_id' => $row[1],
                        'event_name_id' => $row[2],
                        'session_id' => $row[3],
                        'data' => $row[4],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($events) === 1000) {
                        DB::table('log_events')->insert($events);
                        $events = [];
                    }
                }

                if (!empty($events)) {
                    DB::table('log_events')->insert($events);
                }

                fclose($handle);

                return true;
            };
        }

        Concurrency::run($tasks);
    }
}
