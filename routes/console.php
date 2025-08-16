<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Command custom xóa user chưa xác thực
Artisan::command('users:delete-unverified', function () {
    $users = \App\Models\User::whereNull('email_verified_at')
        ->where('created_at', '<', now()->subDay())
        ->get();

    foreach ($users as $user) {
        $user->delete();
    }

    $this->info('Đã xóa ' . count($users) . ' user chưa xác thực email.');
})->describe('Delete users who have not verified email after 1 day');
