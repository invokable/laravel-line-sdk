<?php

namespace Tests\Socialite;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Revolution\Line\Socialite\LineLoginProvider;
use Tests\TestCase;

class LineLoginProviderTest extends TestCase
{
    public function test_instance()
    {
        $provider = Socialite::driver('line-login');

        $this->assertInstanceOf(LineLoginProvider::class, $provider);
    }

    public function test_redirect()
    {
        Socialite::fake('line-login');

        $response = Socialite::driver('line-login')->redirect();

        $this->assertTrue($response->isRedirection());
    }

    public function test_user()
    {
        $fakeUser = (new User)->map([
            'id' => 'test',
            'name' => 'displayName',
            'email' => '',
            'avatar' => 'pictureUrl',
        ]);

        Socialite::fake('line-login', $fakeUser);

        $user = Socialite::driver('line-login')->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('test', $user->getId());
    }
}
