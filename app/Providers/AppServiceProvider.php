<?php

namespace App\Providers;

use App\Models\comments\commentModel;
use App\Models\posts\postsLikeModel;
use App\Models\user\followsModel;
use App\Observers\commentObserver;
use App\Observers\followObserver;
use App\Observers\likeObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        postsLikeModel::observe(likeObserver::class);
        commentModel::observe(commentObserver::class);
        followsModel::observe(followObserver::class);

    }
}
