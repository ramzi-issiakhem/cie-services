<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use http\Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ClientRegistry
     */
    private $registry;
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var User
     */
    private $user;

    public function __construct(UserRepository $repository,RouterInterface $router,ClientRegistry $registry)
    {

        $this->router = $router;
        $this->registry = $registry;
        $this->repository = $repository;
    }

    const LOGIN_ROUTE = "oauth_check";

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('user.login'));
    }

    public function supports(Request $request): ?bool
    {
        if ( self::LOGIN_ROUTE === $request->attributes->get("_route") ) {
            return true;
        }

        return false;
    }

    public function getCredentials(Request $request)
    {
          return $this->fetchAccessToken($this->registry->getClient('facebook'));
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getClient()->fetchUserFromToken($credentials);
        $this->user = $this->repository->findOrCreateFromOauth($facebookUser);
        return $this->user;
    }



    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate("user.login"));
    }

    public  function getClient() {
        return $this->registry->getClient('facebook');
    }


//    public function authenticate(Request $request): PassportInterface
//    {
//        return new Passport(new UserBadge($this->user->getEmail()),new PasswordCredentials($this->user->getPassword()),[
//            new PasswordUpgradeBadge($this->user->getPassword()),
//            new RememberMeBadge()
//        ]);
//    }
}
