<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MemberController extends AbstractController
{

    /**
     * Affiche un espace membre
     * @Route("/member/account", name="member_account")
     * @IsGranted("ROLE_USER")
     */
    public function space(PurchaseRepository $purchaseRepo, MenuRepository $menuRepo)
    {

        $user = $this->getUser();
        $role = $user->getRoles();
        $sommes = [];

        if ($role[0] == "ROLE_ADMIN") {

            return $this->render('admin/index.html.twig', [
                'user' => $user,

            ]);
        }

        // affichage du total de toutes les commandes
        $purchases = $purchaseRepo->findBy(['user' => $user]); //retourne toutes les commandes


        for ($m = 0; $m < count($purchases); $m++) {

            $commande = $purchases[$m]->getMenu(); // la j'ai le menu de la commande 

            $quantity = $purchases[$m]->getQuantity(); // la j'ai la quantité par menu

            // je vais chercher le prix de chaque commande
            foreach ($commande as $prixmenu) {
                $somme = 0;
                $menuPrice = $prixmenu->getPrice() / 100;
                $somme += $menuPrice * $quantity;
                $sommes[] = $somme;
            }
        }

        $totalAmount = array_sum($sommes);

        // fin


        if ($purchases != [] || $purchases == null) {
            //si il y a une commande je l'envoi à la vue
            return $this->render('member/account.html.twig', [
                'total' => $totalAmount,
                'user' => $user,
                'commande' => $purchases,
            ]);
        }
        // sinon je lui envoi uniquement l'user
        return $this->render('member/account.html.twig', [
            'user' => $user,
        ]);
    }
}
