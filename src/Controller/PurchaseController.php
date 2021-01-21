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
     * @Route("/new{id}", name="purchase_new")
     */
    public function new($id, Request $request, MenuRepository $menurepo, Menu $menu): Response
    {

        $quelmenu = $menurepo->findOneBy(['id' => $id]);

        $user = $this->getUser();

        $order = new Purchase;


        $form = $this->createForm(PurchaseType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $order->setUser($user);
            $order->addMenu($quelmenu);
            $order->setRegisteredAt(new DateTime());


            if ($order->getQuantity() == 0) {
                $this->addFlash('success', 'Votre commande doit contenir au moins un menu.');
                return $this->redirectToRoute('menu_index');
            }
            $entityManager->persist($order);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commande est lancée !');

            return $this->redirectToRoute('menu_index');
        }

        return $this->render('order/new.html.twig', [
            'menu' => $quelmenu,
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_admin_show", methods={"GET"})
     */

    public function showAdmin(Purchase $purchase): Response
    {
        return $this->render('admin/purchase/show.html.twig', [
            'order' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     */

    public function show(Purchase $purchase): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(OrderModifType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/edit.html.twig', [
            'order' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_delete", methods={"DELETE"})
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
