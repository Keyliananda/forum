<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statement = rtrim(fake()->sentence(8), '.');

        return [
            'discussion_id' => Discussion::factory(),
            'author_id' => User::factory(),
            'statement' => $statement,
            'slug' => Str::slug($statement),
            'type' => 'factual',
            'status' => 'open',
        ];
    }
}
