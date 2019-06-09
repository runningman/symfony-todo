<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\TodoList;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TodoListTest extends KernelTestCase
{
    /** @var TodoList */
    protected $todoList;

    protected function setUp(): void
    {
        parent::setUp();

        $this->todoList = new TodoList();
        $this->todoList->setTitle('Test list');

        $completedTask = new Task();
        $completedTask->setDescription('Complete task');
        $completedTask->setCompletedAt(new DateTime());
        $this->todoList->addTask($completedTask);

        $task = new Task();
        $task->setDescription('Incomplete task');
        $task->setCompletedAt(new DateTime());
        $this->todoList->addTask($task);
    }

    /**
     * The tod do list automatically gets a uuid on creation.
     *
     * @test
     */
    public function it_automatically_gets_uuid(): void
    {
        $this->assertNotNull($this->todoList->getUuid());
    }

    /**
     * When serialized to json it returns the tasks.
     *
     * @test
     */
    public function it_serializes_to_json(): void
    {
        $todoJson = json_encode($this->todoList);

        $this->assertCount(2, json_decode($todoJson));

        $expected = $this->todoList->getTasks()->map(function (Task $task) {
            return [
                'uuid' => $task->getUuid(),
                'description' => $task->getDescription(),
                'is_complete' => $task->isComplete(),
            ];
        })->toArray();

        $this->assertSame(json_encode($expected), $todoJson);
    }
}