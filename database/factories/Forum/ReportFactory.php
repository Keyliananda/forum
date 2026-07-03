<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discussion = Discussion::factory()->create();

        return [
            'reporter_id' => User::factory(),
            'reportable_type' => Discussion::class,
            'reportable_id' => $discussion->id,
            'reason' => 'other',
            'details' => fake()->sentence(),
            'status' => 'open',
        ];
    }
}
