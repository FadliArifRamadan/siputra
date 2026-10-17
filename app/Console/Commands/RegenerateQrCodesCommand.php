<?php

namespace App\Console\Commands;

use App\Models\Fasilitas;
use App\Services\QrCodeService;
use Illuminate\Console\Command;

class RegenerateQrCodesCommand extends Command
{
    protected $signature = 'qrcode:regenerate {--force : Regenerate tanpa konfirmasi}';
    protected $description = 'Regenerate QR Code untuk semua fasilitas dengan URL production';

    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        parent::__construct();
        $this->qrCodeService = $qrCodeService;
    }

    public function handle(): int
    {
        $fasilitas = Fasilitas::all();
        $total = $fasilitas->count();

        if ($total === 0) {
            $this->warn('Tidak ada fasilitas ditemukan.');
            return Command::SUCCESS;
        }

        $this->info('Total fasilitas: ' . $total);
        $this->info('APP_URL saat ini: ' . config('app.url'));

        if (!$this->option('force')) {
            if (!$this->confirm('Yakin ingin regenerate QR Code untuk semua fasilitas dengan URL di atas?')) {
                $this->warn('Dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $count = 0;
        foreach ($fasilitas as $item) {
            try {
                $this->qrCodeService->regenerate($item);
                $count++;
            } catch (\Exception $e) {
                $this->error("\nGagal regenerate untuk fasilitas #{$item->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Berhasil regenerate QR Code untuk {$count} fasilitas.");

        return Command::SUCCESS;
    }
}