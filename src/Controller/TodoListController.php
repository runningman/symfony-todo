<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Services\TodoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoListController extends AbstractController
{
    /**
     * @var TodoService
     */
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    /**
     * Create a new to do list.
     *
     * @Route("/", name="todoList")
     */
    public function index(Request $request)
    {
        $todoList = new TodoList();

        $form = $this->createForm(TodoListType::class, $todoList)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoService->saveTodoList($todoList);

            return new RedirectResponse($this->todoService->getTodoListViewUrl($todoList));
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
            'tasksUrl' => $this->todoService->getTodoListTasksUrl($todoList),
            'addUrl' => $this->todoService->getTodoListTasksAddUrl($todoList),
        ]);
    }
}
