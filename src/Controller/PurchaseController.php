<?php

namespace App\Controller;

use DateTime;
use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/purchase")
 */

class PurchaseController extends AbstractController
{
    /**
     * @Route("/", name="purchase_admin_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */

    public function indexAdmin(PurchaseRepository $purchaseRepository, UserRepository $userRepository, MenuRepository $menuRepo): Response
    {


        $order = $purchaseRepository->findAll();


        // pour chaque purchase je dois recuperer le client

        foreach ($order as $commande) {
            if ($commande->getUser() != null) {

                $clientId = $commande->getUser()->getId();
            }
            $commandeparclient = $purchaseRepository->findBy(['user' => $clientId]);
        }




        return $this->render('admin/purchase/index.html.twig', [
            'users' => $commandeparclient,
            'orders' => $purchaseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="purchase_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */

    public function index(PurchaseRepository $purchaseRepository, UserRepository $userRepository, MenuRepository $menuRepo): Response
    {


        $order = $purchaseRepository->findAll();

        foreach ($order as $commande) {
            $id = $commande->getUser()->getId();
        }

        if ($order) {

            $client = $userRepository->findBy(['id' => $id]);

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

        if (!isset($user)) {
            $this->addFlash('warning', 'Vous devez être connecté pour commander.');
            return $this->redirectToRoute('security_login');
        } else {

            $order = new Purchase;

            $quelmenu = $menurepo->findOneBy(['id' => $id]); // retourne bien le menu choisi

            $form = $this->createForm(PurchaseType::class, $order);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $form = $this->createForm(PurchaseType::class, $order);
                $form->handleRequest($request);
                // si je n'ai pas encore de commande de ce menu 

                if (isset($menudejacommande)) {
                    if ($order->getQuantity() == 0) {
                        $this->addFlash('success', 'Votre commande doit contenir au moins un menu.');
                        return $this->redirectToRoute('menu_index');
                    }
                }
                $entityManager = $this->getDoctrine()->getManager();
                $order->setUser($user);

                $order->addMenu($quelmenu);
                $user = $this->getUser();

                $quelmenu = $menurepo->findOneBy(['id' => $id]); // retourne bien le menu choisi

                $entityManager = $this->getDoctrine()->getManager();
                $order->setUser($user);
                $order->addMenu($quelmenu);
                $order->setTotal($order->getQuantity() * $quelmenu->getPrice());
                $order->setRegisteredAt(new DateTime());
                $entityManager->persist($order);
                $entityManager->flush();

                return $this->redirectToRoute('member_account');


                $entityManager = $this->getDoctrine()->getManager();

                $order->setTotal($order->getQuantity() * $quelmenu->getPrice() / 100);
                $order->setRegisteredAt(new DateTime());
                $entityManager->persist($order);
                $entityManager->flush();

                return $this->redirectToRoute('menu_index');
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
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/{id}/edit/admin", name="purchase_admin_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_USER")
     */
    public function edit($id, Request $request, PurchaseRepository $purchaseRepo, MenuRepository $menurepo, Purchase $purchase): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // pour recalculer le total 
            $commande = $purchaseRepo->findBy(['user' => $user, 'id' => $id]);
            foreach ($commande as $name) {
                $tab = $name->getMenu();
                foreach ($tab as $menu) {
                    $prixMenu = $menu->getPrice();
                }
            }

            $quantityMenu = $name->getQuantity();

            $purchase->setTotal($prixMenu * $quantityMenu);

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
     * @Route("/{id}/admin", name="purchase_admin_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/{id}", name="purchase_user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Purchase $purchase): Response
    {


        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commande a bien été supprimée !');
        }


        return $this->redirectToRoute('member_account');
    }
}
