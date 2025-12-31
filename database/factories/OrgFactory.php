<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org>
 */
class OrgFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company().' '.fake()->companySuffix();

        return [
            'name' => $name,
            'slug' => Str::slug($name.' '.Str::random(4)),
            'status' => 'active',
        ];
    }
}
