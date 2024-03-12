<?php
// src/Controller/Admin/EmailController.php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\EmailType;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends AbstractController
{
    #[Route("/adminemailing",name: "make_email")]
    public function makeEmailer(Request $request): Response
    {
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // do something interesting here
            dd($data);
        }


        return $this->render('admin/email.html.twig', [
            'form' => $form,
        ]);
    }
    // ...
}
