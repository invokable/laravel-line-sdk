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
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
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
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            // 'LINE' => Bot::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }
}
