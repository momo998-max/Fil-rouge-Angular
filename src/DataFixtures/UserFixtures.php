<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Agence;
use App\Entity\Compte;
use App\DataFixtures\ProfilFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($j=0; $j < 5; $j++) {
            $user = new User();
            if ($j===0) {
                $user -> setRoles("ROLE_ADMINSYSTEME");
            }
            elseif($j<4) {
                $agence = new Agence();
                $compte = new Compte();
                $utilisateur = new User();
                $user -> setRoles("ROLE_ADMINAGENCE");
                $agence->setNom('My Agence '.$j);
                $agence->setAdresse($faker->address);
                $agence->setTelephone('77'.substr(str_shuffle(str_repeat($x='0123456789', ceil(7/strlen($x)) )),1,7));
                $agence->setLattitude($faker->latitude());
                $agence->setLongitude($faker->longitude());
                $agence->addAdmin($user);
                $compte->setSolde(3000000);
                $agence->addCompte($compte);
                $utilisateur -> setRoles("ROLE_UTILISATEUR");
                $utilisateur = $this->setCommon($utilisateur); 
                $manager -> persist($utilisateur);
                $agence->addUtilisateur($utilisateur);
                $manager -> persist($agence);
                $manager -> persist($compte);
            }
            else {
                $user -> setRoles("ROLE_CAISSIER");
            }       

            $user = $this->setCommon($user);               
            $manager -> persist($user);   
        }
            
        $manager -> flush();
    }

    public function setCommon(User $user): User{
        $faker = Factory::create('fr_FR');
        $user -> setUsername(strtolower($faker->name()));
        $user -> setPassword('passe');
        $user->setNom($faker->lastname);
        $user->setPrenom($faker->firstname);
        $user->setAdresse($faker->address);
        $user ->setTelephone('77'.substr(str_shuffle(str_repeat($x='0123456789', ceil(7/strlen($x)) )),1,7));
        return $user;
    }
}
