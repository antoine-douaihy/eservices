<?php

namespace App\Providers;

use App\Support\LaravelRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LaravelRequest::class, function ($app) {
            return LaravelRequest::createFrom($app->make('request'));
        });
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Mail::extend('brevo', function () {
            return new BrevoApiTransport(config('services.brevo.key'));
        });
    }
}
