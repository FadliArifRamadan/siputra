<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRemindersCommand extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send reminders for upcoming bookings and tasks';

    protected ReminderService $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    public function handle(): int
    {
        $this->info('Sending reminders...');

        try {
            $this->reminderService->sendAllReminders();
            $this->info('Reminders sent successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error sending reminders: ' . $e->getMessage());
            Log::error('Reminder error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}