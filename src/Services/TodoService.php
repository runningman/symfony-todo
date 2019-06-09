<?php

namespace App\Services;

use App\Entity\Task;
use App\Entity\TodoList;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class TodoService
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    public function __construct(EntityManagerInterface $manager, RouterInterface $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    /**
     * Persist the to do list.
     *
     * @param TodoList $todoList
     *
     * @return TodoList
     */
    public function saveTodoList(TodoList $todoList): TodoList
    {
        $this->manager->persist($todoList);
        $this->manager->flush($todoList);

        return $todoList;
    }

    /**
     * Create a new task entity and link it to the to do list.
     *
     * @param TodoList $todoList
     *
     * @return Task
     */
    public function createNewTaskInTodoList(TodoList $todoList): Task
    {
        $task = new Task();
        $task->setTodoList($todoList);

        return $task;
    }

    /**
     * Mark task as (in)complete and set date when completed.
     *
     * @param Task $task
     * @param bool $isComplete
     *
     * @return Task
     */
    public function toggleTask(Task $task, bool $isComplete): Task
    {
        $task->setCompletedAt($isComplete ? new DateTime() : null);
        $this->saveTask($task);

        return $task;
    }

    /**
     * Persist the task.
     *
     * @param Task $task
     *
     * @return Task
     */
    public function saveTask(Task $task): Task
    {
        $this->manager->persist($task);
        $this->manager->flush($task);

        return $task;
    }

    /**
     * Return url for to do list view.
     *
     * @param TodoList $todoList
     *
     * @return string
     */
    public function getTodoListViewUrl(TodoList $todoList): string
    {
        return $this->router->generate('todoList.view', ['uuid' => $todoList->getUuid()]);
    }

    /**
     * Return api url to retrieve to do list tasks.
     *
     * @param TodoList $todoList
     *
     * @return string
     */
    public function getTodoListTasksUrl(TodoList $todoList): string
    {
        return $this->router->generate('api.tasks', ['uuid' => $todoList->getUuid()]);
    }

    /**
     * Return api url to add task to to do list.
     *
     * @param TodoList $todoList
     *
     * @return string
     */
    public function getTodoListTasksAddUrl(TodoList $todoList): string
    {
        return $this->router->generate('api.tasks.add', ['uuid' => $todoList->getUuid()]);
    }
}
