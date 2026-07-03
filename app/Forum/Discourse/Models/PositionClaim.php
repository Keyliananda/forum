<?php

namespace App\Forum\Discourse\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PositionClaim extends Pivot
{
    protected $table = 'discussion_position_claims';

    protected $fillable = [
        'position_id',
        'claim_id',
        'author_id',
        'sort_order',
    ];
}
