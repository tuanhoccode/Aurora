<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-unverified-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa tài khoản chưa xác thực sau 1 ngày';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = User::whereNull('email_verified_at')
        ->where('created_at', '<', Carbon::now()->subDay())
        ->delete();

        $this->info("Đã xóa {$deleted} tài khoản chưa xác thực");
    }
}
