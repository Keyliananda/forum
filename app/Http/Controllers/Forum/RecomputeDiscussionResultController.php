<?php

namespace App\Http\Controllers\Forum;

use App\Forum\Discourse\Models\Discussion;
use App\Forum\Discourse\Models\DiscussionResultSnapshot;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RecomputeDiscussionResultController extends Controller
{
    public function __invoke(Discussion $discussion): RedirectResponse
    {
        $weights = [
            'argument_quality' => 25,
            'source_quality' => 25,
            'community' => 20,
            'reputation' => 20,
            'external_signals' => 10,
        ];

        $discussion->load(['positions.claims.arguments.qualityVotes', 'positions.claims.evidence']);

        $positionScores = $discussion->positions->map(function ($position) use ($weights): array {
            $claims = $position->claims;
            $arguments = $claims->flatMap->arguments;
            $quality = (float) $arguments
                ->map(fn ($argument): ?float => $argument->averageQualityScore())
                ->filter()
                ->avg();
            $verifiedEvidence = $claims->flatMap->evidence
                ->where('verification_status', 'verified')
                ->count();

            return [
                'position_id' => $position->id,
                'title' => $position->title,
                'score' => round(($quality * $weights['argument_quality']) + ($verifiedEvidence * $weights['source_quality']), 2),
                'argument_quality' => round($quality, 2),
                'verified_evidence' => $verifiedEvidence,
            ];
        })->sortByDesc('score')->values()->all();

        DiscussionResultSnapshot::query()->create([
            'discussion_id' => $discussion->id,
            'profile_key' => 'balanced',
            'weights' => $weights,
            'position_scores' => $positionScores,
            'breakdown' => ['algorithm' => 'w7.1-simple-weighted-read-model'],
            'computed_at' => now(),
        ]);

        return redirect()->route('forum.discussions.show', $discussion);
    }
}
