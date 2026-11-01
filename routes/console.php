<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console & Scheduled Tasks
|--------------------------------------------------------------------------
|
| Di bawah ini adalah konfigurasi penjadwalan otomatis Finora.
| Task ini dijalankan setiap hari pada pukul 00:01 WIB untuk memproses
| pemotongan tagihan rutin (harian, mingguan, bulanan, tanggal khusus).
|
*/

Schedule::command('finora:process-recurring')
    ->dailyAt('00:01')
    ->description('Finora: Pemrosesan tagihan rutin & biaya langganan otomatis');
