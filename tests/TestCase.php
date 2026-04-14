<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Laravel\Socialite\SocialiteServiceProvider;
use Revolution\Line\Facades\Bot;
use Revolution\Line\Providers\LineServiceProvider;
use Revolution\Line\Providers\LineSocialiteServiceProvider;
use Revolution\Line\Providers\MacroServiceProvider;
use Revolution\Line\Providers\RouteServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Load package service provider.
     */
    protected function getPackageProviders($app): array
    {
        return [
            LineServiceProvider::class,
            RouteServiceProvider::class,
            MacroServiceProvider::class,
            LineSocialiteServiceProvider::class,
            SocialiteServiceProvider::class,
        ];
    }

    /**
     * Load package alias.
     */
    protected function getPackageAliases($app): array
    {
        return [
            // 'LINE' => Bot::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('line.bot.channel_secret', 'token');
    }
}
