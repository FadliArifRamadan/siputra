<?php

namespace App\Helpers;

use App\Models\User;
use App\Services\NotificationService;

class NotificationHelper
{
    public static function send(User $user, string $title, string $message, string $type, ?string $link = null, $reference = null): void
    {
        $service = app(NotificationService::class);
        $service->create($user, $title, $message, $type, $link, $reference);
    }

    public static function sendToRole(string $role, string $title, string $message, string $type, ?string $link = null, $reference = null): void
    {
        $users = User::role($role)->get();
        $service = app(NotificationService::class);
        foreach ($users as $user) {
            $service->create($user, $title, $message, $type, $link, $reference);
        }
    }
}