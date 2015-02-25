<?php
require_once __DIR__.'/vendor/autoload.php';
require_once 'config.php';

use Symfony\Component\HttpFoundation\Request;
use Guzzle\Service\Client as GuzzleClient;

class CamdramProvider extends League\OAuth2\Client\Provider\AbstractProvider {
    public function urlAuthorize() {
        return CAMDRAM_URL.'/oauth/v2/auth';
    }
    public function urlAccessToken() {
        return CAMDRAM_URL.'/oauth/v2/token';
    }
    public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token) {
        return CAMDRAM_URL.'/auth/account.json?access_token='.$token;
    }
    public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token) {
        return $response;
    }
}

$provider = new CamdramProvider([
    'clientId' => API_KEY,
    'clientSecret' => API_SECRET,
    'redirectUri' => 'http://'.$_SERVER['HTTP_HOST'].'/info'
]);


$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider());

$app->get('/', function() {
	return '<a href="/login">Login with Camdram</a>';
});

$app->get('/login', function() use ($app, $provider) {
    $url = $provider->getAuthorizationUrl();
    $app['session']->set('oauth2state', $provider->state);
    return $app->redirect($url);
});

$app->get('/info', function(Request $request) use ($provider) {
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $request->get('code')
    ]);
    $userDetails = $provider->getUserDetails($token);
    $output = "<h1>Hello ".$userDetails->name."</h1>"
          . "<h2>My Shows:</h2><ul>";

    $httpClient = new GuzzleClient();
    $request = $httpClient->get(CAMDRAM_URL.'/auth/account/shows.json?access_token='.$token)->send();
    $response = $request->getBody();
    $shows = json_decode($response, true);
    foreach ($shows as $show) {
        $output .= "<li>" . $show['name'] . "</li>";
    }
    $output .= "</ul>";
    return $output;
});

$app->run();
