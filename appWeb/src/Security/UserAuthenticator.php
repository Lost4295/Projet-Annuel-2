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
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class UserAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;
//
    public const LOGIN_ROUTE = 'login';
//
    private $entityManager;
    private $emailVerifier;

    private $urlGenerator;
//
    public function __construct(EntityManagerInterface $entityManager, EmailVerifier $emailVerifier, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->urlGenerator = $urlGenerator;
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
        $email = $request->request->get('_username') ?? $request->request->all()['user']['email'];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $user->setLastConnDate(new \DateTime('now'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        if (!$user->isVerified()) {
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('toto@titi.com', 'Administrateur Site')) //FIXME À changer, quand on envoira de vrais mails
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        }
        //For example:
        $url = $this->getTargetPath($request->getSession(), 'secured_area');
        dump($url);
        $url = $this->getTargetPath($request->getSession(), 'secured_area') ?? $this->getTargetPath($request->getSession(), 'main') ;
        dump($url);
        if (!$url) {
            $url = $this->urlGenerator->generate('index');
            }
        dd($url);

        return new RedirectResponse($url);
        //return parent::onAuthenticationSuccess($request, $token);

        //throw new \Exception(' TODO : provide a valid redirect inside ' . __FILE__);

        //return $this->redirectToRoute('index');
    }
//
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception): Response|null
    {
        $request->getSession()->getFlashBag()->add('error', 'Identifiants incorrects');
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }
}
