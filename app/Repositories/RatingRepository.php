<?php

namespace App\Repositories;

use App\Models\Rating;
use App\Models\Transaction;
use App\Models\User;

class RatingRepository
{
    /**
     * Create a new rating.
     *
     * @param \App\Models\Transaction $transaction
     * @param \App\Models\User $rater
     * @param \App\Models\User $rated
     * @param int $score
     * @param string|null $comment
     * @return \App\Models\Rating
     */
    public function create(Transaction $transaction, User $rater, User $rated, int $score, ?string $comment = null): Rating
    {
        return Rating::create([
            'transaction_id' => $transaction->id,
            'rater_id' => $rater->id,
            'rated_id' => $rated->id,
            'score' => $score,
            'comment' => $comment,
        ]);
    }

    /**
     * Check if a rating already exists for a given transaction by a specific rater.
     *
     * @param \App\Models\Transaction $transaction
     * @param \App\Models\User $rater
     * @return bool
     */
    public function ratingExists(Transaction $transaction, User $rater): bool
    {
        return Rating::where('transaction_id', $transaction->id)
            ->where('rater_id', $rater->id)
            ->exists();
    }
}
