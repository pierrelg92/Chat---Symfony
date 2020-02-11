<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groupe;
use App\Form\LoginFormType;
use App\Form\SignUpFormType;
use App\Form\CreateGroupFormType;
use App\Form\CreateMessageFormType;



use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserController extends AbstractController
{
    /**
     * @Route("/sign", name="sign")
     */
    public function signUp(UserPasswordEncoderInterface $encode, Request $request){

        $usr = new User;

        $form = $this -> createForm(SignUpFormType::Class, $usr);
        $form -> handleRequest($request);
        
        if ($form -> isSubmitted() && $form -> isValid()) {

            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($usr); // Enregistre l'objet dans le systeme (pas dans la BDD)
            $usr -> setRole('ROLE_USER');

            $file = $form['file']->getData();    //Si le champ est vide on considère que la photo du user sera la photo par défault.
            if (is_object($file))
            {
                $usr -> fileUpload();  //Renome l'image de l'utilisateur, l'enregistre en BDD et dans le dossier public/img.
            }
            
            $password = $usr -> getPassword();
            $newPassword = $encode -> encodePassword($usr, $password);

            $usr -> setPassword($newPassword);
            $manager -> flush(); // Enregistre dans la BDD en executant la ou les requêtes enregistrées dans le systeme

            return $this ->redirectToRoute('login');
        }

        return $this->render('user/SignUp.html.twig', [
            'SignUpForm' => $form -> createView()
        ]);

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function Logout(){}

    /**
     * @Route("/login_check", name="login_check")
     */
    public function LoginCheck(){}


    /**
     * @Route("/", name="login")
     */
    public function Login(AuthenticationUtils $auth){

        $lastUsername = $auth -> getLastUsername();
    
        $error = $auth -> getLastAuthenticationError();

        if($error){
            $this -> addFlash('errors', 'Erreur d\'identifiant !');
        }

        return $this -> render('user/Login.html.twig', [
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/groupe/{$id}", name="send")
     */
    public function SendMessage($id, Request $request, UserInterface $user)
    {
        $message = new Message; // Objet vide de la classe Message

        $form = $this -> createForm(CreateMessageFormType::Class, $message);

        $form -> handleRequest($request); // Elle lie définitivement les infos saisies dans le formulaire a notre objet $message

        if ($form -> isSubmitted() && $form -> isValid()) {

            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($message); // Enregistre l'objet dans le systeme (pas dans la BDD)
            $message -> setDatetime(new \DateTime('now'));
            $message -> setGroupe($id);
            $messsage -> setUser($user);
            $manager -> flush(); // Enregistre dans la BDD en executant la ou les requêtes enregistrées dans le systeme

            return $this ->redirectToRoute('conv');
        }

        return $this->render('groupe/index.html.twig', [
            'CreateMessage' => $form -> createView()
        ]);

    }

     /**
     * @Route("/createGroupe", name="newGroup")
     */

    public function createGroup(Request $request, UserInterface $user){

        $grp = new Groupe;

        $form = $this -> createForm(CreateGroupFormType::Class, $grp);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {

            $manager = $this -> getDoctrine() -> getManager();
            $manager -> persist($grp); // Enregistre l'objet dans le systeme (pas dans la BDD)
            $grp -> setDate(new \DateTime('now'));
            $grp ->addUser($user);
            
            $file = $form['file']->getData();    //Si le champ est vide on considère que la photo du groupe sera la photo par défault.
            if (is_object($file))
            {
                $grp -> fileUpload();  //Renome l'image du groupe, l'enregistre en BDD et dans le dossier public/img.
            }
            // !!!! Ajouter plusieurs user !!!!!
            $manager -> flush(); // Enregistre dans la BDD en executant la ou les requêtes enregistrées dans le systeme

            return $this ->redirectToRoute('index');
            
        }
        return $this -> render('groupe/create.html.twig', [
            'CreateGroup' => $form -> createView()
        ]);
    }






}
