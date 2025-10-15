<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = Setting::all()->keyBy('key');

            $siteSettings = [
                'name' => $settings['site_name']->value ?? 'Nama Situs Default',
                'logo_url' => $settings['site_logo']->value ?? null,
                'address' => $settings['site_address']->value ?? 'Alamat Default',
                'email' => $settings['site_email']->value ?? 'email@default.com',
            ];

            $view->with('siteSettings', $siteSettings);
        });
    }
}