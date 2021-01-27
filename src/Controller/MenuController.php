<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use Cocur\Slugify\Slugify;
use App\Repository\MenuRepository;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/menu")
 */
class MenuController extends AbstractController
{

    /**
     * @Route("/", name="menu_index", methods={"GET"})
     */
    public function index(MenuRepository $menuRepository): Response
    {

        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin", name="menu_admin_index", methods={"GET"})
     */
    public function indexAdmin(MenuRepository $menuRepository): Response
    {


        return $this->render('admin/menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="menu_admin_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);


        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        if ($role[0] == "ROLE_ADMIN") {

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $slugify = new Slugify();
                $slug = $slugify->slugify($menu->getName());
                $menu->setSlug($slug);
                $entityManager->persist($menu);

                $entityManager->flush();
                $this->addFlash('success', 'Votre menu a bien été créé.');
                return $this->redirectToRoute('menu_admin_index');
            }
            return $this->render('admin/menu/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="menu_show", methods={"GET"})
     */
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="menu_admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre menu a bien été modifié.');
            return $this->redirectToRoute('menu_admin_index');
        }

        return $this->render('admin/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="menu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($menu);
            $entityManager->flush();
            $this->addFlash('success', 'Votre menu a bien été supprimé.');
        }

        return $this->redirectToRoute('menu_admin_index');
    }
}
