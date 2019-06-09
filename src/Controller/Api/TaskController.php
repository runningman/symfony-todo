<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\CompleteTaskType;
use App\Form\TaskType;
use App\Services\TodoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends ApiController
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
     * Get all tasks in a to do list.
     *
     * @Route("/api/{uuid}/tasks", name="api.tasks", methods={"GET"})
     */
    public function tasks(TodoList $todoList): JsonResponse
    {
        return new JsonResponse($todoList);
    }

    /**
     * Create a new to do list task.
     *
     * @Route("/api/{uuid}/tasks", name="api.tasks.add", methods={"POST"})
     */
    public function addTask(Request $request, TodoList $todoList): JsonResponse
    {
        $task = $this->todoService->createNewTaskInTodoList($todoList);

        $form = $this->getForm($request, TaskType::class, $task);

        if (!$form->isValid()) {
            return $this->errorResponse($form);
        }

        $this->todoService->saveTask($task);

        return new JsonResponse($task);
    }

    /**
     * Mark a task as completed/not complete.
     *
     * @Route("/api/{uuid}/tasks/{task_uuid}", name="api.tasks.update", methods={"POST"})
     * @ParamConverter("task", options={"mapping": {"task_uuid": "uuid"}})
     */
    public function updateTask(Request $request, TodoList $todoList, Task $task): JsonResponse
    {
        $form = $this->getForm($request, CompleteTaskType::class);

        if (!$form->isValid()) {
            return $this->errorResponse($form);
        }

        $task = $this->todoService->toggleTask($task, $form->get('is_complete')->getData());

        return new JsonResponse($task);
    }
}
