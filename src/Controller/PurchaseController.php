<?php

namespace App\Controller;

use DateTime;
use App\Entity\Menu;
use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Form\OrderModifType;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/order")
 */

class PurchaseController extends AbstractController
{
    /**
     * @Route("/", name="purchase_admin_index", methods={"GET"})
     */

    public function indexAdmin(PurchaseRepository $purchaseRepository, UserRepository $userRepository, MenuRepository $menuRepo): Response
    {

        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        $order = $purchaseRepository->findAll();

        foreach ($order as $commande) {
            $id = $commande->getUser()->getId();
            dump($id); // id de l'user qui a commandé
        }

        //  $menu = $menuRepo->findBy(['purchase' => $order]);
        $menu = $menuRepo->findAll();
        dump($menu);

        if ($order) {

            $client = $userRepository->findBy(['id' => $id]);
            dump($order, $client);
            return $this->render('admin/purchase/index.html.twig', [
                'user' => $client[0],
                'orders' => $purchaseRepository->findAll(),
            ]);
        }



        return $this->render('admin/purchase/index.html.twig', [
            'orders' => $purchaseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="purchase_index", methods={"GET"})
     */

    public function index(PurchaseRepository $purchaseRepository, UserRepository $userRepository, MenuRepository $menuRepo): Response
    {

        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        $order = $purchaseRepository->findAll();

        foreach ($order as $commande) {
            $id = $commande->getUser()->getId();
            dump($id); // id de l'user qui a commandé
        }

        //  $menu = $menuRepo->findBy(['purchase' => $order]);
        $menu = $menuRepo->findAll();
        dump($menu);

        if ($order) {

            $client = $userRepository->findBy(['id' => $id]);
            dump($order, $client);
            return $this->render('purchase/index.html.twig', [
                'user' => $client[0],
                'orders' => $purchaseRepository->findAll(),
            ]);
        }



        return $this->render('purchase/index.html.twig', [
            'orders' => $purchaseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new{id}", name="purchase_new")
     */
    public function new($id, Request $request, MenuRepository $menurepo, PurchaseRepository $purchaseRepo, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        $order = new Purchase;

        $quelmenu = $menurepo->findOneBy(['id' => $id]); // retourne bien le menu choisi

        $purchases = $purchaseRepo->findBy(['user' => $user]); // je dois recuperer les menus existants dans les commandes

        $form = $this->createForm(PurchaseType::class, $order);
        $form->handleRequest($request);

        for ($m = 0; $m < count($purchases); $m++) {

            $commande = $purchases[$m]->getMenu(); // la j'ai le menu de la commande 
            $commandeId = $purchases[$m]->getId(); // la j'ai l'id de la commande 

            foreach ($commande as $nommenu) {

                $menuAchete = $nommenu->getId(); // la j'ai le nom du menu déja commandé
            }

            if ($id == $menuAchete) {

                $this->addFlash('success', 'Vous avez déja ce menu dans vos commandes, vous pouvez en modifier la quantité !');
                return $this->redirectToRoute('purchase_edit', ['id' => $commandeId]);
            } else {

                if ($form->isSubmitted() && $form->isValid()) {

                    if (isset($menudejacommande)) {
                        if ($order->getQuantity() == 0) {
                            $this->addFlash('success', 'Votre commande doit contenir au moins un menu.');
                            return $this->redirectToRoute('menu_index');
                        }
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $order->setUser($user);

                    $order->addMenu($quelmenu);

                    $order->setRegisteredAt(new DateTime());
                    $entityManager->persist($order);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre commande est lancée !');
                    return $this->redirectToRoute('menu_index');
                }
            }
        }

        return $this->render('purchase/new.html.twig', [
            'menu' => $quelmenu,
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="purchase_admin_show", methods={"GET"})
     */

    public function showAdmin(Purchase $purchase): Response
    {
        return $this->render('admin/purchase/show.html.twig', [
            'order' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}", name="purchase_show", methods={"GET"})
     */

    public function show(Purchase $purchase): Response
    {
        return $this->render('purchase/show.html.twig', [
            'order' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="purchase_admin_edit", methods={"GET","POST"})
     */
    public function editAdmin(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('purchase_admin_index');
        }

        return $this->render('admin/purchase/edit.html.twig', [
            'order' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="purchase_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre commande a bien été modifiée !');
            return $this->redirectToRoute('member_account');
        }

        return $this->render('purchase/edit.html.twig', [
            'order' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="purchase_admin_delete", methods={"DELETE"})
     */
    public function deleteAdmin(Request $request, Purchase $purchase): Response
    {

        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('member_account');
    }

    /**
     * @Route("/{id}", name="purchase_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Purchase $purchase): Response
    {

        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('member_account');
    }
}



            // si le menu est deja commandé il faut augmenter sa quantité et pas creer une commande
            /*
            if (isset($choix)) {
                $choix->setQuantity(+1);
                $order->setUser($user);
                // $order->addMenu($quelmenu);
                $order->setRegisteredAt(new DateTime());
                $em->persist($order);
                $em->flush();
                $this->addFlash('success', 'Votre commande est lancée !');

                return $this->redirectToRoute('menu_index');
            }
*/
