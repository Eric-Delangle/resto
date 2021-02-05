<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use  Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_admin_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {

        $liste = $userRepository->findAll();/* ce sont ces elements que je veux paginer */


        return $this->render('admin/user/index.html.twig', [

            'users' => $paginator->paginate(
                $liste,/* ce sont ces elements que je veux paginer */

                $request->query->getInt('page', 1),
                6
            )
        ]);

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_admin_show", methods={"GET"})
     */
    public function showAdmin(User $user): Response
    {
        $role = $this->getUser()->getRoles();

        if ($role[0] == "ROLE_ADMIN") {

            return $this->render('admin/user/show.html.twig', [
                'user' => $user,

            ]);
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if (!isset($user)) {

            $role = $this->getUser()->getRoles();
            if ($role[0] == "ROLE_ADMIN") {

                return $this->render('admin/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),

                ]);
            }
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Vos informations ont bien été modifiées !');
            return $this->redirectToRoute('member_account');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user, SessionInterface $session): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);

            $user = $this->getUser();
            $this->container->get('security.token_storage')->setToken(null);

            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été supprimé !');
        }

        return $this->redirectToRoute('home');
    }



    /**
     * @Route("/{id}/admin", name="user_admin_delete", methods={"DELETE"})
     */
    public function deleteAdmin(Request $request, User $user, SessionInterface $session): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);

            $entityManager->flush();
            $this->addFlash('success', 'Ce client a bien été supprimé (de la base de données bien sur) !');
        }

        return $this->redirectToRoute('member_account');
    }
}
