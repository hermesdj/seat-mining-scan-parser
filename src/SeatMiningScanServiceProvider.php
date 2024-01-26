<?php

namespace HermesDj\Seat\SeatMiningScanParser;

use Seat\Services\AbstractSeatPlugin;

class SeatMiningScanServiceProvider extends AbstractSeatPlugin
{
    public function boot(): void
    {
        $this->add_routes();

        $this->add_views();

        $this->add_translations();

        $this->add_publications();

        $this->add_migrations();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/Menu/package.sidebar.tools.php', 'package.sidebar.tools.entries');
    }

    private function add_routes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }

    private function add_publications(): void
    {
        $this->publishes([
            __DIR__ . '/resources/css' => public_path('web/css'),
            __DIR__ . '/resources/img' => public_path('web/img'),
            __DIR__ . '/resources/js' => public_path('web/js'),
        ], ['public', 'seat']);
    }

    private function add_translations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'scan-parser');
    }

    private function add_views(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'scan-parser');
    }

    private function add_migrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
    }

    public function getName(): string
    {
        return "Seat Mining Scan Parser";
    }

    public function getPackageRepositoryUrl(): string
    {
        return "https://github.com/hermesdj/seat-mining-scan-parser";
    }

    public function getPackagistPackageName(): string
    {
        return "seat-mining-scan-parser";
    }

    public function getPackagistVendorName(): string
    {
        return "hermesdj";
    }
}