<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AiAnalysis;

class AiAnalysisPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AiAnalysis $analysis): bool
    {
        if ($user->isAgronom()) {
            return $analysis->user_id === $user->id;
        }

        return true; // admin + manager
    }

    public function create(User $user): bool
    {
        return true; // все могут делать анализ
    }

    public function delete(User $user, AiAnalysis $analysis): bool
    {
        return $user->isAdmin();
    }
}
