<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Warning;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class ModerationSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Request $request;
    public function __construct(EntityManagerInterface $em, Request $request)
    {
        $this->em = $em;
        $this->request = $request;
    }

    public function onFormPreSubmit(PreSubmitEvent $event): void
    {
        $form = $event->getForm();
        // dd($form->getConfig()->getRequestHandler(), $form);
        $forbiddenWords = file(__DIR__ . "/blacklist.txt"); // Replace with your list of forbidden words
        $formData = $event->getData();
        foreach ($formData as $key => $value) {
            $field = $form->get($key);
            $ff = $field->getConfig()->getType()->getBlockPrefix();
            if ($ff === 'text' || $ff === 'textarea' || $ff ==="email") {
                foreach ($forbiddenWords as $word) {
                    $word = trim($word);
                    if (preg_match("/$word/ui", $value)) {
                        $field->addError(new FormError('This field contains forbidden words'));
                        $formData[$key] = str_replace($word, '*****', $value);
                        $lastemail = $this->request->getSession()->all()["_security.last_username"];
                        $user = $this->em->getRepository(User::class)->findOneBy(["email"=>$lastemail]);
                        $warning = new Warning();
                        $warning->setUser($user);
                        $warning->setIp($this->request->getClientIp());
                        $warning->setForm($form->getName());
                        $warning->setField($ff);
                        $warning->setWord($word);
                        $warning->setDate(new \DateTime());
                        $this->em->persist($warning);
                        $this->em->flush();
                        $this->request->getSession()->getFlashBag()->add("danger","forbiddenword");
                        if ($ff === "email") {
                            $formData[$key] = $lastemail;
                        }
                        $event->setData($formData);
                        return;
                    }
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onFormPreSubmit',
        ];
    }
}
