<?php

namespace Modules\Tag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TagModel>
 */
class TagModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TagModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word().'-'.fake()->uuid();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
