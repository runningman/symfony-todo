<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\CompleteTaskType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
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
        // TODO: better ajax json handling?
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTodoList($todoList);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskRepository->save($task);

            return new JsonResponse($task);
        }

        return new JsonResponse(
            ['failed' => $this->getFormErrors($form)],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Mark a task as completed/not complete.
     *
     * @Route("/api/{uuid}/tasks/{task_uuid}", name="api.tasks.update", methods={"POST"})
     * @ParamConverter("task", options={"mapping": {"task_uuid": "uuid"}})
     */
    public function updateTask(Request $request, TodoList $todoList, Task $task): JsonResponse
    {
        // TODO: better ajax json handling?
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CompleteTaskType::class, null, [
            'csrf_protection' => false,
        ])->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $isComplete = $form->get('is_complete')->getData();
            $task->setCompletedAt($isComplete ? new DateTime() : null);
            $this->taskRepository->save($task);

            return new JsonResponse($task);
        }

        return new JsonResponse(
            ['failed' => $this->getFormErrors($form)],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    protected function getFormErrors(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        foreach ($form as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
