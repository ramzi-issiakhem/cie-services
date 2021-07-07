<?php
namespace  App\Controller;




use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends  AbstractController {

    public function test(ClientRegistry $registry) {

        $client = $registry
            ->getClient('facebook');

        $user = $client->fetchUser();
        // do something with all this new power!
        $user->getFirstName();

        return $this->render('pages/home.html.twig',[
            'events' => [],
            'products' => []
        ]);
    }
}



?>