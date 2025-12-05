<?php

namespace Aura\Notifications;

use Aura\Notifications\Livewire\NotificationsBell;
use Aura\Notifications\Livewire\NotificationsPanel;
use Aura\Notifications\Resources\SystemUpdate as SystemUpdateResource;
use Aura\Notifications\Services\NotificationService;
use Aura\Notifications\Services\SystemUpdateService;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('aura-notifications')
            ->hasConfigFile('aura-notifications')
            ->hasViews('aura-notifications')
            ->hasRoutes('web')
            ->hasMigrations([
                'create_system_updates_table',
                'create_system_update_reads_table',
            ]);
    }

    public function packageRegistered(): void
    {
        // Register services
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(SystemUpdateService::class);

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/aura-notifications.php',
            'aura-notifications'
        );
    }

    public function packageBooted(): void
    {
        // Register Livewire components
        $this->registerLivewireComponents();

        // Register Aura Resource after Aura boots
        $this->app->booted(function () {
            if (class_exists('\Aura\Base\Aura') && app()->bound('aura')) {
                try {
                    $aura = app(\Aura\Base\Aura::class);
                    $aura->registerResources([
                        SystemUpdateResource::class,
                    ]);
                } catch (\Throwable $e) {
                    logger()->error('Failed to register Aura notification resources: '.$e->getMessage());
                }
            }
        });

        // Register permissions
        $this->registerPermissions();

        // Publish migrations
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_system_updates_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_system_updates_table.php'),
                __DIR__.'/../database/migrations/create_system_update_reads_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_system_update_reads_table.php'),
            ], 'aura-notifications-migrations');

            // Publish config
            $this->publishes([
                __DIR__.'/../config/aura-notifications.php' => config_path('aura-notifications.php'),
            ], 'aura-notifications-config');
        }
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        // Register our enhanced notifications panel, overriding Aura's default
        Livewire::component('aura::notifications', NotificationsPanel::class);
        Livewire::component('aura-notifications::bell', NotificationsBell::class);
        Livewire::component('aura-notifications::panel', NotificationsPanel::class);
    }

    /**
     * Register permissions for the notification system.
     */
    protected function registerPermissions(): void
    {
        Gate::define('view-notifications', function ($user) {
            return true; // All authenticated users can view notifications
        });

        Gate::define('view-system-updates', function ($user) {
            return true; // All authenticated users can view updates
        });

        Gate::define('manage-system-updates', function ($user) {
            if (method_exists($user, 'isAuraGlobalAdmin')) {
                return $user->isAuraGlobalAdmin();
            }

            if (method_exists($user, 'isSuperAdmin')) {
                return $user->isSuperAdmin();
            }

            return false;
        });
    }
}
