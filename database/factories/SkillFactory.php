<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'category_id' => Category::factory(),
            'proficiency_level' => fake()->numberBetween(1, 5),
            'is_active' => fake()->boolean(80),
            'description' => fake()->paragraphs(3, true),
            'tags' => [
                ['value' => fake()->word()],
                ['value' => fake()->word()],
            ],
            'notes' => fake()->sentence(),
            'archived' => false,
        ];
    }
}
