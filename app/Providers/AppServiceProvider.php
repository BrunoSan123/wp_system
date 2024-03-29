<?php

namespace App\Providers;

use App\Services\CsvReaderService;
use App\Services\EditorService;
use App\Services\LoginService;
use App\Services\ScheduleService;
use App\Services\PostContentService;
use App\Services\GoogleDriveService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EditorService::class,function($app){
            return new EditorService();
        });

        $this->app->singleton(CsvReaderService::class,function($app){
            return new CsvReaderService();
        });

        $this->app->singleton(PostContentService::class,function($app){
            return new PostContentService();
        });

        $this->app->singleton(LoginService::class,function($app){
            return new LoginService();
        });

        $this->app->singleton(ScheduleService::class,function($app){
            return new ScheduleService();
        });

        $this->app->singleton(GoogleDriveService::class,function($app){
            return new GoogleDriveService();
        });



        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
