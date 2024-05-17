<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;

class ModerationSubscriber implements EventSubscriberInterface
{
    public function onFormPreSubmit(PreSubmitEvent $event): void
    {
        // Todo récupérer le formulaire
        $form = $event->getForm();

        // TODO check s'il y a des textType ou des TextareaType, puis check si ces champs contiennent des mots interdits
        $forbiddenWords = ['badword1', 'badword2']; // Replace with your list of forbidden words
        $formData = $event->getData();
        foreach ($formData as $key => $value) {
            $field = $form->get($key);
            if ($field->getConfig()->getType()->getBlockPrefix() === 'text' || $field->getConfig()->getType()->getBlockPrefix() === 'textarea') {
                foreach ($forbiddenWords as $word) {
                    if (strpos($value, $word) !== false) {
                        $formData[$key] = str_replace($word, '***', $value);
                        // TODO: Notify the user about the forbidden word
                    }
                }
            }
        }

        $event->setData($formData);


    }

    public static function getSubscribedEvents(): array
    {
        return [
            'form.pre_submit' => 'onFormPreSubmit',
        ];
    }
}
