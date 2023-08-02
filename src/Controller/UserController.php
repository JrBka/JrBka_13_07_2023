<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * This function displays the user list
     * @Route("/users", name="user_list")
     */
    public function listAction(UserRepository $repository): Response
    {
        return $this->render('user/list.html.twig', ['users' => $repository->findAll()]);
    }

    /**
     * This function displays the creation of user page and creates a user
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPlainPassword($plainPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * This function displays the user edition page and edits a user
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $role = $form->get('roles')->getData();

            if ($role[0] != null){
                $user->setRoles($role[0]);
            }else{
                $user->setRoles($user->getRole());
            }

            if ($plainPassword != null){
                $user->setPlainPassword($plainPassword);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
