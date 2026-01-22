<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Athlete;
use Illuminate\Support\Str;

class GenerateMissingAthleteQRCodes extends Command
{
    protected $signature = 'athletes:generate-qr';
    protected $description = 'Generate QR codes for athletes missing qr_code';

    public function handle()
    {
        $athletes = Athlete::whereNull('qr_code')
            ->orWhere('qr_code', '')
            ->get();

        foreach ($athletes as $athlete) {
            $athlete->qr_code = 'ATH-' . strtoupper(Str::uuid());
            $athlete->save();

            $this->info("Generated QR for: {$athlete->name}");
        }

        $this->info('All missing QR codes generated.');
    }
}
