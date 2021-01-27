<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MemberController extends AbstractController
{

    /**
     * Affiche un espace membre
     * @Route("/member/account", name="member_account")
     */
    public function space(PurchaseRepository $purchaseRepo, MenuRepository $menuRepo)
    {

        $user = $this->getUser();
        $role = $user->getRoles();
        if ($role[0] == "ROLE_ADMIN") {

            return $this->render('admin/index.html.twig', [
                'user' => $user,

            ]);
        }
        // $menu = $menuRepo->findAll();
        $commande = $purchaseRepo->findBy(['user' => $user]); //retourne toutes les commandes



        if ($commande != [] || $commande == null) {
            //si il y a une commande je l'envoi à la vue
            return $this->render('member/account.html.twig', [
                'user' => $user,
                'commande' => $commande,
            ]);
        }
        // sinon je lui envoi uniquement l'user
        return $this->render('member/account.html.twig', [
            'user' => $user,
        ]);
    }
}
