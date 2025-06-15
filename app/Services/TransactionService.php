<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\RatingRepository;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionService
{
    public $transactionRepository;
    public $ratingRepository;

    public function __construct(TransactionRepository $transactionRepository, RatingRepository $ratingRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * Get all transactions for a specific user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTransactions(User $user): Collection
    {
        return $this->transactionRepository->getUserTransactions($user);
    }

    /**
     * Submit a rating for a completed transaction.
     *
     * @param int $transactionId
     * @param \App\Models\User $rater
     * @param int $score
     * @param string|null $comment
     * @return \App\Models\Rating|null
     */
    public function submitRating(int $transactionId, User $rater, int $score, ?string $comment = null): ?\App\Models\Rating
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction || !$transaction->canBeRatedBy($rater)) {
            return null; // Transaction not found, not completed, or already rated by this user
        }

        // Determine who is being rated
        $rated = ($rater->id === $transaction->buyer_id) ? $transaction->seller : $transaction->buyer;

        return $this->ratingRepository->create($transaction, $rater, $rated, $score, $comment);
    }

    /**
     * Get filtered transactions for admin reports.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionReports(array $filters): Collection
    {
        return $this->transactionRepository->getFilteredTransactions($filters);
    }
}