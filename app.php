<?php
require_once __DIR__.'/vendor/autoload.php';
require_once 'config.php';

use Symfony\Component\HttpFoundation\Request;
use Guzzle\Service\Client as GuzzleClient;

$provider = new \Acts\Camdram\OAuth2\Provider\Camdram([
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
    $options = ['scope' => ['user_shows', 'user_orgs']];
    $url = $provider->getAuthorizationUrl($options);
    $app['session']->set('oauth2state', $provider->getState());
    return $app->redirect($url);
});

$app->get('/info', function(Request $request) use ($provider) {
    if (!$request->query->has('code'))
    {
        return $this->redirect('/');
    }

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $request->query->get('code')
        ]);
        $userDetails = $provider->getResourceOwner($token);
        $output = "<h1>Hello ".$userDetails->getName()."</h1>";
        
        $output .= "<h2>My Shows:</h2><ul>";
        $shows = $provider->getAuthenticatedData('/auth/account/shows.json', $token);
        foreach ($shows as $show) {
            $output .= "<li>" . $show['name'] . "</li>";
        }
        $output .= "</ul>";
        
        $output .= "<h2>My Organisations:</h2><ul>";
        $orgs = $provider->getAuthenticatedData('/auth/account/organisations.json', $token);
        foreach ($orgs as $org) {
            $output .= "<li>" . $org['name'] . "</li>";
        }
        $output .= "</ul>";
        
        return $output;
    }
    catch (\Acts\Camdram\OAuth2\Provider\Exception\CamdramIdentityProviderException $e)
    {
        return '<p>An authentication error has occurred</p>';
    }
});

$app->run();
