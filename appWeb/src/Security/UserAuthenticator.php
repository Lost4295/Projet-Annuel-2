<?php

namespace App\Security;
// 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

//Class unused. but do not delete, function authenticate is used in LoginFormAuthenticator
class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
// 
    public const LOGIN_ROUTE = 'login';
// 
    private $entityManager;
    private $emailVerifier;
// 
    public function __construct(private UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, EmailVerifier $emailVerifier)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
    }
// 
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
// 
        $request->getSession()->set(Security::LAST_USERNAME, $email);
// 
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }
// 
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $log = new LoginSuccessHandler($this->entityManager, $this->emailVerifier, $this->urlGenerator);
        return $log->onAuthenticationSuccess($request, $token);
    }
// 
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
// 