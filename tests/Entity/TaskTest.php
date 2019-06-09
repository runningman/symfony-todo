<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    /** @var Task */
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->task = new Task();
        $this->task->setDescription('Test description');
    }

    /**
     * The task automatically gets a uuid on creation.
     *
     * @test
     */
    public function it_automatically_gets_uuid(): void
    {
        $this->assertNotNull($this->task->getUuid());
    }

    /**
     * It returns bool depending on whether completed at date is set.
     *
     * @dataProvider providerIsComplete
     * @test
     */
    public function it_returns_status_based_on_completed_at_date($completedAt, $expectedValue): void
    {
        $this->task->setCompletedAt($completedAt);

        $this->assertSame($expectedValue, $this->task->isComplete());
    }

    public function providerIsComplete(): array
    {
        return [
            [new \DateTime, true],
            [null, false],
        ];
    }

    /**
     * When serialized to json it exposes uuid, description and is_complete
     *
     * @test
     */
    public function it_serializes_to_json(): void
    {
        $taskJson = json_encode($this->task);

        $expected = [
            'uuid' => $this->task->getUuid(),
            'description' => 'Test description',
            'is_complete' => false,
        ];

        $this->assertSame(json_encode($expected), $taskJson);
    }
}