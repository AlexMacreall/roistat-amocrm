<?php

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Dotenv\Dotenv;

$rootdir = dirname( __FILE__, 4);

include_once $rootdir . '/vendor/autoload.php';

$dotenv = new Dotenv;
$dotenv->load('../.env');

$apiClient = new AmoCRMApiClient(
    $_ENV['CLIENT_ID'],
    $_ENV['CLIENT_SECRET'],
    $_ENV['CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['ACCOUNT_DOMAIN']);

$rawToken = json_decode(file_get_contents('../token.json'), 1);
$token = new AccessToken($rawToken);

$apiClient->setAccessToken($token);

$apiClient->onAccessTokenRefresh(function ($token){
    file_put_contents('../token.json', json_encode($token->jsonSerialize(), JSON_PRETTY_PRINT));
});

$account = $apiClient->account()->getCurrent();

echo '<pre>';
print_r($account->toArray());