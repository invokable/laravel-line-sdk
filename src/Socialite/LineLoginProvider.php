<?php

namespace Revolution\Line\Socialite;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class LineLoginProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'profile',
        'openid',
    ];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC3986;

    /**
     * Indicates if PKCE should be used.
     *
     * @var bool
     */
    protected $usesPKCE = true;

    /**
     * Permission required to use 'email' scope.
     */
    protected ?string $email = null;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://access.line.me/oauth2/v2.1/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return 'https://api.line.me/oauth2/v2.1/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse($code)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $form_params = $this->getTokenFields($code);

        $response = $this->getHttpClient()->post(
            $this->getTokenUrl(),
            compact('headers', 'form_params')
        );

        $response = json_decode($response->getBody(), true);

        if (Arr::exists($response, 'id_token')) {
            $this->getEmail(Arr::string($response, 'id_token'));
        }

        return $response;
    }

    /**
     * email is included in the id_token but cannot be obtained without permission.
     */
    protected function getEmail(string $id_token): void
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $form_params = [
            'id_token' => $id_token,
            'client_id' => $this->clientId,
        ];

        $response = $this->getHttpClient()->post(
            'https://api.line.me/oauth2/v2.1/verify',
            compact('headers', 'form_params')
        );

        $payload = json_decode($response->getBody(), true);

        $this->email = data_get($payload, 'email');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.line.me/v2/profile',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map(
            [
                'id' => $user['userId'],
                'nickname' => $user['displayName'] ?? '',
                'name' => $user['displayName'] ?? '',
                'email' => $this->email ?? '',
                'avatar' => $user['pictureUrl'] ?? '',
            ]
        );
    }
}
