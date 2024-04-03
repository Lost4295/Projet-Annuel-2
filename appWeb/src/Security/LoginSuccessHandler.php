<?php


namespace App\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\CustomAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class LoginSuccessHandler extends CustomAuthenticationSuccessHandler
{

    private $entityManager;
    private $emailVerifier;
    private $urlGenerator;

    public function __construct( EntityManagerInterface $entityManager, EmailVerifier $emailVerifier, urlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('_username')]);
        $user->setLastConnDate(new \DateTime('now'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        if (!$user->isVerified()) {
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('toto@titi.com', 'Administrateur Site')) //FIXME À changer
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        }
        // For example:


        return new RedirectResponse($this->urlGenerator->generate('index'));
        // return parent::onAuthenticationSuccess($request, $token);
        
        // throw new \Exception(' TODO : provide a valid redirect inside ' . __FILE__);

        // return $this->redirectToRoute('index');
    }
}