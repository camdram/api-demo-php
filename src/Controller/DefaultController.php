<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
     /**
      * @Route("/")
      */
    public function index()
    {
        return new Response('<a href="/login">Login with Camdram</a>');
    }

    /**
     * @Route("/login")
     */
    public function login(Request $request)
    {
        $options = ['scope' => ['user_shows', 'user_orgs']];
        $provider = $this->createProvider();
        $url = $provider->getAuthorizationUrl($options);
        $request->getSession()->set('oauth2state', $provider->getState());
        return $this->redirect($url);
    }

    /**
     * @Route("info")
     */
    public function info(Request $request)
    {
        if (!$request->query->has('code'))
        {
            return $this->redirect('/');
        }

        try {
            $provider = $this->createProvider();
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

            return new Response($output);
        }
        catch (\Acts\Camdram\OAuth2\Provider\Exception\CamdramIdentityProviderException $e)
        {
            return new Response('<p>An authentication error has occurred</p>');
        }
    }

    private function createProvider()
    {
        return new \Acts\Camdram\OAuth2\Provider\Camdram([
            'clientId' => getenv('API_KEY'),
            'clientSecret' => getenv('API_SECRET'),
            'redirectUri' => 'http://'.$_SERVER['HTTP_HOST'].'/info'
        ]);
    }
}
