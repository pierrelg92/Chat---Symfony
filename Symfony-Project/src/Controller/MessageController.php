<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/index", name="message")
     */
    public function recupAllMessages()
    {

        $repository = $this -> getDoctrine() -> getRepository(Message::class);
        $messages = $repository -> findAll();

        return $this->render('groupe/index.html.twig');
    }

    /**
     * @Route("/message", name="message")
     */
    public function message($id)
     {
         // 2 requetes 
        $repo = $this -> getDoctrine() -> getRepository(Message::class);
        $message = $repo -> find($id);
        
         //afficher la vue
         return $this->render('groupe/index.html.twig', [
             'message' => $message
         ]);
     }

     public function States($state){
        $repo = $this -> getDoctrine() -> getRepository(Message::class);
        $state = $repo -> find($state);

        return $this->render('groupe/index.html.twig', [
            'state' => $state
        ]);
     }

}
