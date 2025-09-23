<?php

namespace App\Console\Commands;

use App\Models\EventName;
use App\Models\Market;
use Illuminate\Console\Command;
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
}
