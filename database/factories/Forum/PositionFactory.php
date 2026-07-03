<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(6), '.');

        return [
            'discussion_id' => Discussion::factory(),
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(),
            'status' => 'open',
            'sort_order' => 0,
        ];
    }
}
