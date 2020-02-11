<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use App\Entity\Message;

use App\Form\CreateMessageFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupeController extends AbstractController
{
  

    /**
     * @Route("/groupe/{id}", name="conv")
     */
    public function displayGroup($id,Request $request, UserInterface $user){
        // recupérer un group par son id
        // renvoyer les données à la vue

        $repo = $this -> getDoctrine() -> getRepository(Groupe::Class);
        $group = $repo -> find($id);

        $message = new Message; // Objet vide de la classe Message

        $form = $this -> createForm(CreateMessageFormType::Class, $message);

        $form -> handleRequest($request); // Elle lie définitivement les infos saisies dans le formulaire a notre objet $message

        if ($form -> isSubmitted() && $form -> isValid()) {

            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($message); // Enregistre l'objet dans le systeme (pas dans la BDD)
            $message -> setDatetime(new \DateTime('now'));
            $message -> setGroupe($group);
            $message -> setState(1);
            $message -> setUser($user);
            $manager -> flush(); // Enregistre dans la BDD en executant la ou les requêtes enregistrées dans le systeme

           
        }

        return $this -> render('groupe/index.html.twig', [
            'group' => $group,
            'CreateMessage' => $form -> createView(),
            'groupId' => $id
        ]);
    }

     /**
     * @Route("/index", name="index")
     */
     public function recupAllGroups()
     {
         ///je recupere tous les infos des User
         $repository = $this -> getDoctrine() -> getRepository(User::class);
         $groups = $repository -> findAll();

         return $this->render('user/index.html.twig');
     }
     
     /**
     * @Route("/listOfUser/{groupId}", name="listOfUser")
     */
    public function listOfUser($groupId)
    {
        $repo = $this -> getDoctrine() -> getRepository(User::Class);
        $users = $repo -> findAll();

        $repoG = $this -> getDoctrine() -> getRepository(Groupe::Class);
        $group = $repoG -> find($groupId);

        return $this -> render('groupe/listOfUser.html.twig', [
            'users' => $users,
            'group' => $group,
            'groupId' => $groupId
        ]);
    }

     /**
     * @Route("/addUserIntoGr/{userId}/{groupId}", name="addUserIntoGr")
     */
    public function addUser($userId, $groupId)
    {
        $manager = $this -> getDoctrine() -> getManager();  //Initialisation du manager.

        $repoU = $this -> getDoctrine() -> getRepository(User::Class);
        $user = $repoU -> find($userId);  //Récuperation de l'utilisateur depuis la bdd via son id et stockage de ce dernier dans une variable.

        $repoG = $this -> getDoctrine() -> getRepository(Groupe::Class);
        $group = $repoG -> find($groupId);  //Récuperation du groupe depuis la bdd via son id et stockage de ce dernier dans une variable.

        $group->addUser($user);  //Ajout de l'utilisateur au groupe.

        $manager -> flush();  //Enregistrement des modifs en bdd.

        return $this ->redirectToRoute('index');
    }

     /**
     * @Route("/deleteUserFromGr/{userId}/{groupId}", name="deleteUserFromGr")
     */
    public function deleteUser($userId, $groupId)
    {
        $manager = $this -> getDoctrine() -> getManager();  //Initialisation du manager.

        $repoU = $this -> getDoctrine() -> getRepository(User::Class);
        $user = $repoU -> find($userId);  //Récuperation de l'utilisateur depuis la bdd via son id et stockage de ce dernier dans une variable.

        $repoG = $this -> getDoctrine() -> getRepository(Groupe::Class);
        $group = $repoG -> find($groupId);  //Récuperation du groupe depuis la bdd via son id et stockage de ce dernier dans une variable.

        $group->removeUser($user);  //Suppression de l'utilisateur du groupe.

        $manager -> flush();  //Enregistrement des modifs en bdd.

        return $this ->redirectToRoute('index');
    }

}
