<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Purchase;
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
        $menuName = ['Le gourmand', 'Le gourmet', 'La fringale', 'Le repus', 'La grosse faim'];
        $entreeName = ['Salade de chèvre chaud', 'Delice de la mer', 'Ribambelle de légumes', 'Oeuf mimoza'];
        $platName = ['Frites saucisses', 'Hamburger maison', 'Ratatouille', 'spaghettis Bolognaise', 'Raclette'];
        $fromageName = ['Camenbert', 'Gruyère', 'Roquefort', 'Chèvre', 'Gouda'];
        $dessertName = ['Flan', 'Fondant au chocolat', 'Tarte tatin', 'Profiteroles', 'Banana split'];
        $boissonName = ['Eau minérale', 'Vin', 'Bière', 'Vin', 'Soda'];
        $priceName = ['20 €', '30 €', '15 €', '10 €', '40 €'];

        for ($u = 0; $u < 30; $u++) {
            $user = new User;
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

        for ($m = 0; $m < 15; $m++) {
            $menu = new Menu;

            $menu->setName($faker->randomElement($menuName));
            $menu->setEntree($faker->randomElement($entreeName));
            $menu->setPlat($faker->randomElement($platName));
            $menu->setFromage($faker->randomElement($fromageName));
            $menu->setDessert($faker->randomElement($dessertName));
            $menu->setBoisson($faker->randomElement($boissonName));
            $menu->setPrice($faker->randomElement($priceName));
            $slug = $slugify->slugify($menu->getName());
            $menu->setSlug($slug);
            $menus[] = $menu;
            $manager->persist($menu);
        }


        for ($o = 0; $o < mt_rand(1, 5); $o++) {

            $purchase = new Purchase;
            $purchase->setUser($user);
            $purchase->setQuantity(mt_rand(1, 5));
            $purchase->setRegisteredAt($faker->dateTimeBetween($startDate = '-6 months', $endDate = 'now'));

            $selectedMenus = $faker->randomElements($menus, mt_rand(3, 5));

            foreach ($selectedMenus as $menu) {
                $purchase->addMenu($menu);
            }

            $manager->persist($purchase);
        }



        $manager->flush();
    }
}
