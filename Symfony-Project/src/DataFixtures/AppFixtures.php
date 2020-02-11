<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Groupe;
use App\Entity\User;
use App\Entity\Message;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for($i = 1; $i < 4; $i ++)
        {
            $user = new User;
            $user -> setUsername('user' . $i);
            $user -> setEmail('user' . $i . '@gmail.com');
            $user -> setRole('ROLE_USER');
            $user -> setPassword('Ynov2020');
            $user -> setPhoto("user" . $i .".jpg");
            $manager -> persist($user);
        }
        //transfert bdd
        $manager->flush();
    
        $repoUser = $manager -> getRepository(User::class);
        $users = $repoUser -> findAll();
        $nbUser = count($users);

        for($k=1; $k<=4; $k ++){
            $grp = new Groupe;
            $grp -> setName('Groupe ' .$k);
            $grp -> setPhoto("grp" . $k . ".jpg");
            $grp -> setDate(new \DateTime('now'));
            
            for($j = 0; $j < 3; $j++){
                $indice = rand(0, $nbUser - 1 );
                // Si user n'est pas déjà dans le groupe
                $grp-> addUser($users[$indice]);
                $grp -> addUser($users[$j]);
                for($j=1; $j<=2; $j ++){
                    $msg = new Message;
                    $msg -> setContent('Holisticly utilize user friendly paradigms with clicks-and-mortar meta-services. 
                    Appropriately grow excellent resources and covalent customer service.
                    ');
                    $msg -> setDateTime(new \DateTime('now'));
                    $msg -> setUser($users[$indice]);
                    $msg -> setUser($users[$j]);
                    $msg -> setState(rand(1,3));
                    
                    $manager ->persist($msg);
                    $grp -> addMessage($msg);
                }
            }
            $manager ->persist($grp);
        }
        $manager->flush();
    }
}
