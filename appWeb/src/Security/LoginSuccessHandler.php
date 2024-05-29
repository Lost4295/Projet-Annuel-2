<?php


namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\CustomAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginSuccessHandler extends CustomAuthenticationSuccessHandler
{
    use TargetPathTrait;
    private $entityManager;
    private $emailVerifier;
    private $urlGenerator;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, EmailVerifier $emailVerifier, urlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        $email = $request->request->get('_username') ?? $request->request->all()['user']['email'];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user->hasRole(User::ROLE_NON_USER)){
            $this->security->logout(false);
            return new RedirectResponse($this->urlGenerator->generate('index', ['e' => 'unauthorized']));
        }
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
        // For example:

        $url = $this->getTargetPath($request->getSession(), 'secured_area') ?? $this->getTargetPath($request->getSession(), 'main') ;
        if (!$url) {
            $url = $this->urlGenerator->generate('index');
        }

        return new RedirectResponse($url);
        // return parent::onAuthenticationSuccess($request, $token);

        // throw new \Exception(' TODO : provide a valid redirect inside ' . __FILE__);

        // return $this->redirectToRoute('index');
    }
}
