<?php

namespace  App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController {


    function login() {

        return $this->render('pages/login.html.twig');
    }
}