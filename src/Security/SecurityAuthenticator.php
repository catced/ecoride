<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class SecurityAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    
    public const LOGIN_ROUTE = 'app_login';
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(private UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authorizationChecker)
    { 
        $this->authorizationChecker = $authorizationChecker;
    }

    public function authenticate(Request $request): Passport
    {
       // $email = $request->getPayload()->getString('email');
        $email = $request->request->get('email');
       // $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
       $request->getSession()->set('_security_last_username', $email);
        return new Passport(
            new UserBadge($email),
           // new PasswordCredentials($request->getPayload()->getString('password')),
            // [
            //     new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),           
            // ]
            new PasswordCredentials($request->request->get('password')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),           
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        } elseif ($this->authorizationChecker->isGranted("ROLE_ADMIN")) {
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        } 
        
           
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
