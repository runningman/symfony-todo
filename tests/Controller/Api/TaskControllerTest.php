<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends TestCase
{
    /**
     * Uuid to use for testing.
     *
     * @var string
     */
    const TEST_UUID = '00000000-1234-1234-1234-123456781234';

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureLoader->load([
            'fixtures/todolist.yml',
            'fixtures/task.yml',
        ]);
    }

    /**
     * The tasks are returned as json for a list.
     *
     * @test
     */
    public function it_returns_all_tasks_in_a_list(): void
    {
        /** @var TodoList $todoList */
        $todoList = $this->doctrine->getRepository(TodoList::class)->find(1);
        // Refresh so relation is loaded.
        $this->doctrine->getManager()->refresh($todoList);

        $this->client->request('GET', '/api/' . $todoList->getUuid() . '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        $expectedTasks = $todoList->getTasks()->map(function (Task $task) {
            return [
                'uuid' => $task->getUuid(),
                'description' => $task->getDescription(),
                'is_complete' => $task->isComplete(),
            ];
        })->toArray();

        $this->assertCount(2, $response);
        $this->assertSame($expectedTasks, $response);
    }

    /**
     * When a list has no tasks it returns an empty array.
     *
     * @test
     */
    public function it_returns_empty_array_when_list_has_no_tasks(): void
    {
        $this->client->request('GET', '/api/' . self::TEST_UUID . '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $response = $this->getJsonResponse();

        $this->assertCount(0, $response);
        $this->assertSame([], $response);
    }

    /**
     * When a list doesn't exist a 404 is triggered.
     *
     * @test
     */
    public function it_returns_404_when_list_doesnt_exist(): void
    {
        $this->client->request('GET', '/api/invalid-list/tasks');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * A new task can be added to a list.
     *
     * @test
     */
    public function it_adds_a_task_to_a_list(): void
    {
        $response = $this->postJson('/api/' . self::TEST_UUID . '/tasks', [
            'description' => 'My new task',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertNotNull($response['uuid']);
        $this->assertSame('My new task', $response['description']);
        $this->assertSame(false, $response['is_complete']);
    }

    /**
     * The task description can't be blank.
     *
     * @test
     */
    public function it_cant_add_an_empty_task_to_a_list(): void
    {
        $response = $this->postJson('/api/' . self::TEST_UUID . '/tasks', [
            'description' => '',
        ]);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $expectedError = ['description' => ['This value should not be blank.']];
        $this->assertSame($expectedError, $response);
    }

    /**
     * A task can be marked as complete.
     *
     * @test
     */
    public function it_can_mark_a_task_as_complete(): void
    {
        /** @var Task $task */
        $task = $this->doctrine->getRepository(Task::class)->find(1);
        $url = '/api/' . self::TEST_UUID . '/tasks/' . $task->getUuid();

        $response = $this->postJson($url, ['is_complete' => true]);
        $this->assertSame([
            'uuid' => $task->getUuid(),
            'description' => $task->getDescription(),
            'is_complete' => true,
        ], $response);

        // Completed at date was set.
        $this->doctrine->getManager()->refresh($task);
        $this->assertNotNull($task->getCompletedAt());
    }

    /**
     * A task can be marked as incomplete.
     *
     * @test
     */
    public function it_can_mark_a_task_as_incomplete(): void
    {
        /** @var Task $task */
        $task = $this->doctrine->getRepository(Task::class)->find(1);
        $url = '/api/' . $task->getTodoList()->getUuid() . '/tasks/' . $task->getUuid();

        $response = $this->postJson($url, ['is_complete' => false]);
        $this->assertSame([
            'uuid' => $task->getUuid(),
            'description' => $task->getDescription(),
            'is_complete' => false,
        ], $response);

        // Completed at date was cleared.
        $this->doctrine->getManager()->refresh($task);
        $this->assertNull($task->getCompletedAt());
    }
}