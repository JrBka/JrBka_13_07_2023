<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * This function displays the list of tasks to do
     * @Route("/tasks/todo", name="task_list_todo")
     */
    public function listToDo(TaskRepository $repository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repository->findBy(['isDone'=>false])]);
    }

    /**
     * This function displays the list of tasks done
     * @Route("/tasks/done", name="task_list_done")
     */
    public function listDone(TaskRepository $repository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $repository->findBy(['isDone'=>true])]);
    }

    /**
     * This function displays the creation of task page and creates a task
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * This function displays the edition of task page and edits a task
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * This function defines if a task is done or to do
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $em) : Response
    {
        $task->toggle(!$task->isDone());
        $em->persist($task);
        $em->flush();

        if ($task->isDone()){
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('task_list_done');
        }else{
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminé.', $task->getTitle()));

            return $this->redirectToRoute('task_list_todo');
        }
    }

    /**
     * This function remove a task
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $em): Response
    {
        $taskAuthor = $task->getUser();
        $currentUser = $this->getUser();

        if ($taskAuthor === $currentUser || ($taskAuthor->getUserIdentifier() == "anonyme" && $currentUser->getRoles()[0] === "ROLE_ADMIN")){

            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

        }else{

            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de faire ca.');
        }

        return $this->redirectToRoute('task_list_todo');
    }
}
