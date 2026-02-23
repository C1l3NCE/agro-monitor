<?php

namespace App\Policies;

use App\Models\Field;
use App\Models\User;

class FieldPolicy
{
    /**
     * Просмотр списка полей
     */
    public function viewAny(User $user): bool
    {
        return true; // Все авторизованные могут открыть раздел
    }

    /**
     * Просмотр конкретного поля
     */
    public function view(User $user, Field $field): bool
    {
        if ($user->isAgronom()) {
            return $field->user_id === $user->id;
        }

        // admin + manager
        return true;
    }

    /**
     * Создание поля
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Обновление поля
     */
    public function update(User $user, Field $field): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Удаление поля
     */
    public function delete(User $user, Field $field): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Field $field): bool
    {
        return false;
    }

    public function forceDelete(User $user, Field $field): bool
    {
        return false;
    }
}
