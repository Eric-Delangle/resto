<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Order;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        $menus = [];

        for ($u = 0; $u < 30; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setFirstName($faker->firstNameMale());
            $user->setLastName($faker->lastName());
            $user->setEmail("email+" . $u . "@email.com");
            $slug = $slugify->slugify($user->getFirstName() . ' ' . $user->getLastName());
            $user->setSlug($slug);
            $user->setCity($faker->city());
            $user->setPassword($hash);
            $user->setRoles(["ROLE_USER"]);
            $user->setAddress($faker->address());
            $user->setTel($faker->phoneNumber());
            $user->setRegisteredAt($faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'));
            $manager->persist($user);
        }

        for ($m = 0; $m < 10; $m++) {
            $menu = new menu();
            // recuperation d'une valeur aléatoire d'un tableau créé a la voléee 
            $menu->setName(array_rand(array_flip(['Le gourmand', 'Le gourmet', 'La fringale', 'Le repus', 'La grosse faim'])));
            $menu->setEntree(array_rand(array_flip(['Salade de chèvre chaud', 'Delice de la mer', 'Ribambelle de légumes', 'Oeuf mimoza'])));
            $menu->setPlat(array_rand(array_flip(['Frites saucisses', 'Hamburger maison', 'Ratatouille', 'spaghettis Bolognaise', 'Raclette'])));
            $menu->setFromage(array_rand(array_flip(['Camenbert', 'Gruyère', 'Roquefort', 'Chèvre', 'Gouda'])));
            $menu->setDessert(array_rand(array_flip(['Flan', 'Fondant au chocolat', 'Tarte tatin', 'Profiteroles', 'Banana split'])));
            $menu->setBoisson(array_rand(array_flip(['Eau minérale', 'Vin', 'Bière', 'Vin', 'Soda'])));
            $menu->setPrice(array_rand(array_flip(['20 €', '30 €', '15 €', '10 €', '40 €'])));
            $slug = $slugify->slugify($menu->getName());
            $menu->setSlug($slug);
            $menus[] = $menu;
            $manager->persist($menu);
            for ($o = 0; $o < mt_rand(1, 5); $o++) {

                $order = new order();
                $order->setSlug($menu->getSlug());
                $order->setUser($user);
                $menu->setCommande($order);

                $manager->persist($order);
            }
        }





        $manager->flush();
    }
}
