<?php

use App\Console\Commands\SyncCategoriesFromApi;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('syncs categories from external api into database', function () {
    // Arrange: fake the HTTP call to the exact URL our command uses.
    Http::fake([
        env('RINVEX_API_CATEGORIES_URL_V1') => Http::response([
            ['name' => 'Developer', 'id' => '3'],
            ['name' => 'Engineer',  'id' => '28'],
            ['name' => 'Manager',   'id' => '31'],
        ], 200),
    ]);

    expect(Category::count())->toBe(0);

    // Act: run the command
    $exitCode = Artisan::call('categories:sync');

    // Assert: command succeeded
    expect($exitCode)->toBe(SyncCategoriesFromApi::SUCCESS);

    // Assert: database has the new categories with correct api_id
    expect(Category::pluck('name')->all())
        ->toEqualCanonicalizing(['Developer', 'Engineer', 'Manager']);

    expect(Category::where('api_id', '3')->where('name', 'Developer')->exists())->toBeTrue();
    expect(Category::where('api_id', '28')->where('name', 'Engineer')->exists())->toBeTrue();
    expect(Category::where('api_id', '31')->where('name', 'Manager')->exists())->toBeTrue();

    // Assert: no duplicates when run again (idempotent behavior)
    Artisan::call('categories:sync');
    expect(Category::count())->toBe(3);
});

it('returns failure when api request fails', function () {
    // Arrange: return HTTP 500 from API
    Http::fake([
        env('RINVEX_API_CATEGORIES_URL_V1') =>
            Http::response(null, 500),
    ]);

    // Act
    $exitCode = Artisan::call('categories:sync');

    // Assert
    expect($exitCode)->toBe(SyncCategoriesFromApi::FAILURE);
    expect(Category::count())->toBe(0);
});

it('returns failure when api returns empty array', function () {
    // Arrange: API returns empty list
    Http::fake([
        env('RINVEX_API_CATEGORIES_URL_V1') =>
            Http::response([], 200),
    ]);

    // Act
    $exitCode = Artisan::call('categories:sync');

    // Assert
    expect($exitCode)->toBe(SyncCategoriesFromApi::FAILURE);
    expect(Category::count())->toBe(0);
});
