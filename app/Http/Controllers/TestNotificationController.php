<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class TestNotificationController extends Controller
{
    /**
     * Send a test system notification to the authenticated user.
     */
    public function __invoke(Request $request): string
    {
        /** @var User $user */
        $user = $request->user();

        $systemNotification = new SystemNotification('Hello World', 'This is a test notification.');

        $user->notify($systemNotification);

        return 'Notification sent!';
    }
}
