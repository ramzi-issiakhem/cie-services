<?php
// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAuthenticator extends AbstractAuthenticator
{

    /**
     * @var TranslatorInterface
     */
    private $translator;
    private $csrfTokenManager;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(TranslatorInterface $translator,CsrfTokenManagerInterface $csrfTokenManager,UrlGeneratorInterface $urlGenerator)
    {
        $this->translator = $translator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->urlGenerator = $urlGenerator;
    }

    const LOGIN_ROUTE = "user.login";

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
           && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = [
            'email' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException($this->translator->trans("errors.auth.token"));
        }


        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

            if (empty($credentials)) {
                throw new CustomUserMessageAuthenticationException($this->translator->trans("errors.auth.credentials.empty"));
            }

            return new Passport(new UserBadge($credentials['email']),new PasswordCredentials($credentials['password']),[
                new CsrfTokenBadge('authenticate', $credentials['csrf_token']),
                new PasswordUpgradeBadge($credentials['password']),
                new RememberMeBadge()
            ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
         $this->urlGenerator->generate('user.login');
         return null;
    }
}





//
//namespace App\Security;
//
//use App\Entity\User;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
//use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//use Symfony\Component\Security\Core\Exception\AuthenticationException;
//use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
//use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
//use Symfony\Component\Security\Core\Security;
//use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Security\Core\User\UserProviderInterface;
//use Symfony\Component\Security\Csrf\CsrfToken;
//use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
//use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
//use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
//use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
//use Symfony\Component\Security\Http\Util\TargetPathTrait;
//use Symfony\Contracts\Translation\TranslatorInterface;
//
//class UserAuthenticator extends AbstractGuardAuthenticator implements PasswordAuthenticatedInterface
//{
//    use TargetPathTrait;
//
//    public const LOGIN_ROUTE = 'user.login';
//
//    private $entityManager;
//    private $urlGenerator;
//    private $csrfTokenManager;
//    private $passwordEncoder;
//    private $translator;
//
//    public function __construct(TranslatorInterface  $translator,EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
//    {
//        $this->entityManager = $entityManager;
//        $this->urlGenerator = $urlGenerator;
//        $this->csrfTokenManager = $csrfTokenManager;
//        $this->passwordEncoder = $passwordEncoder;
//        $this->translator = $translator;
//    }
//
//    public function supports(Request $request)
//    {
//        return self::LOGIN_ROUTE === $request->attributes->get('_route')
//            && $request->isMethod('POST');
//    }
//
//    public function getCredentials(Request $request)
//    {
//
//        $credentials = [
//            'emailOrName' => $request->request->get('_username'),
//            'password' => $request->request->get('_password'),
//            'csrf_token' => $request->request->get('_csrf_token'),
//        ];
//
//        $request->getSession()->set(
//            Security::LAST_USERNAME,
//            $credentials['emailOrName']
//        );
//
//        return $credentials;
//    }
//
//    public function getUser($credentials, UserProviderInterface $userProvider)
//    {
//        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
//        if (!$this->csrfTokenManager->isTokenValid($token)) {
//            throw new InvalidCsrfTokenException();
//        }
//        $emailOrName = $credentials['emailOrName'];
//        $user = $this->entityManager->getRepository(User::class)->findOneByUsernameOrEmail($emailOrName);
//
//        if (!$user) {
//            throw new UsernameNotFoundException($this->translator->trans('authentication.user.notfound'));
//        }
//
//        return $user;
//    }
//
//    public function checkCredentials($credentials, UserInterface $user)
//    {
//        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
//    }
//
//
//    /**
//     * Used to upgrade (rehash) the user's password automatically over time.
//     */
//    public function getPassword($credentials): ?string
//    {
//        return $credentials['password'];
//    }
//
//
//    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
//    {
//        return null;
//
//
//    }
//
//
//
//
//
//    public function start(Request $request, AuthenticationException $authException = null)
//    {
//        $this->urlGenerator->generate('user.login');
//    }
//
//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
//    {
//            $this->urlGenerator->generate('user.login');
//    }
//
//    public function supportsRememberMe()
//    {
//        return false;
//    }
//}
