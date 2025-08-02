<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearAppCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-cache {--type=all : Type of cache to clear (all, views, config, route, cache)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application cache efficiently';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'views':
                $this->clearViewsCache();
                break;
            case 'config':
                $this->clearConfigCache();
                break;
            case 'route':
                $this->clearRouteCache();
                break;
            case 'cache':
                $this->clearApplicationCache();
                break;
            default:
                $this->clearAllCache();
                break;
        }

        $this->info('Cache cleared successfully!');
    }

    private function clearViewsCache()
    {
        $this->info('Clearing views cache...');
        Artisan::call('view:clear');
    }

    private function clearConfigCache()
    {
        $this->info('Clearing config cache...');
        Artisan::call('config:clear');
    }

    private function clearRouteCache()
    {
        $this->info('Clearing route cache...');
        Artisan::call('route:clear');
    }

    private function clearApplicationCache()
    {
        $this->info('Clearing application cache...');
        
        // Clear specific cache keys
        Cache::forget('home_page_data');
        Cache::forget('shop_filters_*');
        
        // Clear all cache
        Cache::flush();
    }

    private function clearAllCache()
    {
        $this->info('Clearing all cache...');
        
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        
        // Clear specific application cache
        $this->clearApplicationCache();
    }
} 