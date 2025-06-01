<?php

namespace Tests\Socialite;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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
        $request = m::mock(Request::class);
        $request->shouldReceive('session->put')->andReturn();
        $request->shouldReceive('session->get')->andReturn();

        $provider = new LineLoginProvider($request, 'client_id', 'client_secret', 'redirect');
        $provider->stateless();
        $response = $provider->redirect();

        $this->assertStringStartsWith('https://access.line.me', $response->getTargetUrl());
    }

    public function test_user()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('input')
            ->with('code')
            ->andReturn('fake-code');
        $request->shouldReceive('session->put')->andReturn();
        $request->shouldReceive('session->get')->andReturn();
        $request->shouldReceive('session->pull')->andReturn();

        $stream = m::mock(StreamInterface::class);
        $stream->allows('__toString')->andReturns(json_encode(['access_token' => 'fake-token']));

        $accessTokenResponse = m::mock(ResponseInterface::class);
        $accessTokenResponse->shouldReceive('getBody')->andReturn($stream);

        $stream = m::mock(StreamInterface::class);
        $stream->allows('__toString')->andReturns(json_encode([
            'userId' => $userId = 'test',
            'displayName' => 'displayName',
            'pictureUrl' => 'pictureUrl',
        ]));

        $basicProfileResponse = m::mock(ResponseInterface::class)->makePartial();
        $basicProfileResponse->shouldReceive('getBody')->andReturn($stream);

        $guzzle = m::mock(Client::class);
        $guzzle->shouldReceive('post')->once()->andReturn($accessTokenResponse);
        $guzzle->shouldReceive('get')
            ->with('https://api.line.me/v2/profile', [
                'headers' => [
                    'Authorization' => 'Bearer fake-token',
                ],
            ])->andReturn($basicProfileResponse);

        $provider = new LineLoginProvider($request, 'client_id', 'client_secret', 'redirect');
        $provider->stateless();
        $provider->setHttpClient($guzzle);

        $user = $provider->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($userId, $user->getId());
    }
}
