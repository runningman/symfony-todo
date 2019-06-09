<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoListController extends AbstractController
{
    /**
     * @var TodoListRepository
     */
    private $todoListRepository;

    public function __construct(TodoListRepository $todoListRepository)
    {
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * Create a new to do list.
     *
     * @Route("/", name="todoList")
     */
    public function index(Request $request)
    {
        $todoList = new TodoList();

        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListRepository->save($todoList);

            return new RedirectResponse($this->generateUrl('todoList.view', ['uuid' => $todoList->getUuid()]));
        }

        return $this->render('todo/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * View a to do list.
     *
     * @Route("/{uuid}", name="todoList.view")
     */
    public function view(TodoList $todoList)
    {
        return $this->render('todo/tasks.html.twig', [
            'todoList' => $todoList,
            'tasksUrl' => $this->generateUrl('api.tasks', ['uuid' => $todoList->getUuid()]),
            'addUrl' => $this->generateUrl('api.tasks.add', ['uuid' => $todoList->getUuid()]),
        ]);
    }
}
