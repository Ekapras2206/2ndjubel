<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository
{
    /**
     * Create a new transaction.
     *
     * @param array $data
     * @return \App\Models\Transaction
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Find a transaction by ID.
     *
     * @param int $id
     * @return \App\Models\Transaction|null
     */
    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    /**
     * Get all transactions for a specific user (as buyer or seller).
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserTransactions(User $user): Collection
    {
        return Transaction::where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id)
                          ->with(['product', 'buyer', 'seller', 'rating'])
                          ->latest('transaction_date')
                          ->get();
    }

    /**
     * Get transactions based on filters for reporting.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredTransactions(array $filters): Collection
    {
        $query = Transaction::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['start_date'])) {
            $query->where('transaction_date', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->where('transaction_date', '<=', $filters['end_date']);
        }
        if (isset($filters['buyer_id'])) {
            $query->where('buyer_id', $filters['buyer_id']);
        }
        if (isset($filters['seller_id'])) {
            $query->where('seller_id', $filters['seller_id']);
        }

        return $query->with(['product', 'buyer', 'seller'])->get();
    }

    /**
     * Update a transaction's status.
     *
     * @param \App\Models\Transaction $transaction
     * @param string $status
     * @return bool
     */
    public function updateStatus(Transaction $transaction, string $status): bool
    {
        $transaction->status = $status;
        return $transaction->save();
    }
}