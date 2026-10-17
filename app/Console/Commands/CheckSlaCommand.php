<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\SlaService;
use Illuminate\Console\Command;

class CheckSlaCommand extends Command
{
    protected $signature = 'sla:check';
    protected $description = 'Check SLA status for all reports and send notifications if breached';

    protected SlaService $slaService;
    protected NotificationService $notificationService;

    public function __construct(SlaService $slaService, NotificationService $notificationService)
    {
        parent::__construct();
        $this->slaService = $slaService;
        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $this->info('Checking SLA status...');

        // Update all SLA status
        $this->slaService->updateAllSlaStatus();

        // Get breached reports
        $breached = $this->slaService->getBreachedReports();

        if ($breached->isNotEmpty()) {
            $this->info('Found ' . $breached->count() . ' breached reports.');

            // Send notifications to all RTK users
            $rtkUsers = \App\Models\User::role('rtk')->get();

            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    '⚠️ SLA Breached!',
                    'Terdapat ' . $breached->count() . ' laporan yang melewati target penyelesaian. Segera tindak lanjuti.',
                    'sla_breached',
                    route('rtk.dashboard'),
                    null
                );
            }

            // Also send email to RTK (first user only to avoid spam)
            if ($rtkUsers->isNotEmpty()) {
                // Email notification handled via service
                $this->notificationService->sendEmail(
                    $rtkUsers->first(),
                    'sla_breached',
                    (object) [
                        'count' => $breached->count(),
                        'link' => route('rtk.dashboard'),
                    ]
                );
            }
        } else {
            $this->info('No breached reports found.');
        }

        $this->info('SLA check completed.');

        return Command::SUCCESS;
    }
}