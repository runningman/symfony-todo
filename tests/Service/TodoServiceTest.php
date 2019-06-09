<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Services\TodoService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TodoServiceTest extends KernelTestCase
{
    /**
     * @var MockObject
     */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * When called the to do list is persisted and flushed.
     *
     * @test
     */
    public function it_saves_the_todo_list(): void
    {
        $todoList = new TodoList();
        $todoList->setTitle('Test list');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->getService()->saveTodoList($todoList);
    }

    /**
     * A task is linked to a to do list.
     *
     * @test
     */
    public function it_creates_a_new_task_for_a_list(): void
    {
        $todoList = new TodoList();
        $todoList->setTitle('Test list');

        $task = $this->getService()->createNewTaskInTodoList($todoList);

        $this->assertSame(get_class($task), Task::class);
        $this->assertSame($todoList, $task->getTodoList());
    }

    /**
     * A task can be marked as complete.
     *
     * @test
     */
    public function it_marks_task_as_complete(): void
    {
        $task = new Task();
        $task->setDescription('Test task');
        $task->setCompletedAt(null);

        $task = $this->getService()->toggleTask($task, true);

        $this->assertNotNull($task->getCompletedAt());
    }

    /**
     * A task can be marked as incomplete.
     *
     * @test
     */
    public function it_marks_task_as_incomplete(): void
    {
        $task = new Task();
        $task->setDescription('Test task');
        $task->setCompletedAt(new DateTime());

        $task = $this->getService()->toggleTask($task, false);

        $this->assertNull($task->getCompletedAt());
    }

    /**
     * When called the task is persisted and flushed.
     *
     * @test
     */
    public function it_saves_the_task(): void
    {
        $task = new Task();
        $task->setDescription('Test task');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->getService()->saveTask($task);
    }

    /**
     * Create the service with mocked entity manager.
     *
     * @return TodoService
     */
    protected function getService(): TodoService
    {
        $kernel = static::bootKernel();
        $router = $kernel->getContainer()->get('router');

        return new TodoService($this->entityManager, $router);
    }
}