<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can update the order status.
     */
    public function updateOrderStatus(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }
}
