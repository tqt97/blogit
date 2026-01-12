<?php

namespace Modules\Tag\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TagModel::factory()->count(1000)->create();
    }
}
