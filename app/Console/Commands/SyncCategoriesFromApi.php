<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCategoriesFromApi extends Command
{
    protected $signature = 'categories:sync';

    protected $description = 'Sync categories from Rinvex API';

    public function handle(): int
    {
        try {
            $this->info('Fetching categories from Rinvex API...');

            $response = Http::timeout(15)
                ->get('https://692f9b9b778bbf9e006def35.mockapi.io/api/Rinvex/categories/categories');

            if ($response->failed()) {
                $this->error('Failed to fetch categories from API');
                return Command::FAILURE;
            }

            $apiCategories = $response->json();

            if (empty($apiCategories)) {
                $this->warn('No categories returned from API');
                return Command::FAILURE;
            }

            $this->info('Processing ' . count($apiCategories) . ' categories...');

            $synced = 0;
            $skipped = 0;

            foreach ($apiCategories as $categoryData) {
                $exists = Category::where('name', $categoryData['name'])
                    ->orWhere('api_id', $categoryData['id'])
                    ->exists();

                if (!$exists) {
                    Category::create([
                        'name' => $categoryData['name'],
                        'api_id' => $categoryData['id'],
                    ]);
                    $synced++;
                } else {
                    $skipped++;
                }
            }

            $this->info("✅ Sync completed!");
            $this->info("   Added: {$synced}");
            $this->info("   Skipped: {$skipped}");
            $this->info("   Total in database: " . Category::count());

            return Command::SUCCESS;

        } catch (\Exception $e) {
            Log::error('Category sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
