<?php
// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailerController extends AbstractController
{
    private  MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    #[Route('/email')]
    public function sendEmail()
    {
        for ($i = 0; $i < 10; $i++) {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
            // ->attachFromPath('/path/to/some-file.txt')
            ;
        $this->mailer->send($email);
        }
        return $this->render('base.html.twig');

        // ...
    }
    public function sendMail($destinataire, $sujet, $message)
    {
        $email = (new Email())
            ->from('contact@pariscaretakerservices.fr')
            ->to($destinataire)
            ->subject($sujet)
            ->text($message)
            ->html($message);
        $this->mailer->send($email);
    }
}