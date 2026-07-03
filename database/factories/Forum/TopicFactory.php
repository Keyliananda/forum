<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Space;
use App\Forum\Discourse\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Topic>
 */
class TopicFactory extends Factory
{
    protected $model = Topic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word().' '.fake()->unique()->word();
        $slug = Str::slug($name);

        return [
            'space_id' => Space::factory(),
            'parent_id' => null,
            'name' => Str::headline($name),
            'slug' => $slug,
            'path' => $slug,
            'depth' => 0,
            'description' => fake()->sentence(),
            'created_by_user_id' => null,
        ];
    }

    public function childOf(Topic $topic): static
    {
        return $this->state(fn (array $attributes): array => [
            'space_id' => $topic->space_id,
            'parent_id' => $topic->id,
        ]);
    }
}
