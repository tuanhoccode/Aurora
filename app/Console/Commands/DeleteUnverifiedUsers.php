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
    protected $signature = 'users:delete-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa tài khoản chưa xác thực trong 24h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('email_verified_at')
        ->where('created_at', '<', Carbon::now()->subDay())->get();

        foreach($users as $user){
            $this->info("Xóa user ID: {$user->id}, email: {$user->email}");
            $user->delete();
        } 
        $this->info('Hoàn tất xóa tài khoản chưa xác thực');
    }
}
