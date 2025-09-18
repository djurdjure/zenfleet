<?php

namespace Database\Factories;

use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocumentCategoryFactory extends Factory
{
    protected $model = DocumentCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
            // Ajoutez d'autres colonnes si présentes dans votre schéma
            // 'parent_id' => null,
        ];
    }
}
