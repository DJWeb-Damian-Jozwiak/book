<?php

namespace Tests\Scheduler;

use DateTime;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\Encryption\EncryptionService;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Scheduler\Contracts\JobContract;
use DJWeb\Framework\Scheduler\Queue\DatabaseQueue;
use DJWeb\Framework\Scheduler\QueueFactory;
use DJWeb\Framework\Web\Application;
use PDOStatement;
use Tests\BaseTestCase;
use Tests\Helpers\TestJob;

class DatabaseQueueTest extends BaseTestCase
{
    private Application $app;
    private TestJob $job;

    protected function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->job = new TestJob(
            id: '123',
            title: 'Test Job'
        );
        $this->app->bind('base_path', dirname(__DIR__));
        $security = new KeyGenerator()->generateKey();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')->willReturnCallback(fn($key) => match ($key) {
            'app.key' => $security,
            'queue.default' => 'database',
            default => null,
        });
        $this->app->set(ConfigContract::class, $config);

    }

    public function testPush(): void
    {
        // Mock Insert Query Builder
        $insertBuilder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);

        $insertBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $insertBuilder->expects($this->once())
            ->method('values')
            ->with($this->callback(function($values) {
                return isset($values['id'])
                    && isset($values['payload'])
                    && isset($values['available_at']);
            }))
            ->willReturnSelf();

        $insertBuilder->expects($this->once())
            ->method('execute')
            ->willReturn($stmt);

        $this->app->set(InsertQueryBuilderContract::class, $insertBuilder);

        $queue = QueueFactory::make();
        $jobId = $queue->push($this->job);

        $this->assertNotEmpty($jobId);
    }

    public function testLater(): void
    {
        $delay = new DateTime('2024-01-01 12:00:00');

        // Mock Insert Query Builder
        $insertBuilder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);

        $insertBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $insertBuilder->expects($this->once())
            ->method('values')
            ->with($this->callback(function($values) use ($delay) {
                return isset($values['id'])
                    && isset($values['payload'])
                    && $values['available_at'] === $delay->format('Y-m-d H:i:s');
            }))
            ->willReturnSelf();

        $insertBuilder->expects($this->once())
            ->method('execute')
            ->willReturn($stmt);

        $this->app->set(InsertQueryBuilderContract::class, $insertBuilder);

        $queue = new DatabaseQueue();
        $jobId = $queue->later($delay, $this->job);

        $this->assertNotEmpty($jobId);
    }

    public function testDelete(): void
    {
        // Mock Delete Query Builder
        $deleteBuilder = $this->createMock(DeleteQueryBuilderContract::class);

        $deleteBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $deleteBuilder->expects($this->once())
            ->method('where')
            ->with('id', '=', '123')
            ->willReturnSelf();

        $deleteBuilder->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->app->set(DeleteQueryBuilderContract::class, $deleteBuilder);

        $queue = new DatabaseQueue();
        $queue->delete('123');
    }

    public function testSize(): void
    {
        // Mock Select Query Builder
        $selectBuilder = $this->createMock(SelectQueryBuilderContract::class);

        $selectBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('select')
            ->with(['count(*) as num'])
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('first')
            ->willReturn(['num' => 5]);

        $this->app->set(SelectQueryBuilderContract::class, $selectBuilder);

        $queue = new DatabaseQueue();
        $size = $queue->size();

        $this->assertEquals(5, $size);
    }

    public function testPop(): void
    {
        // Mock Select and Delete Query Builders
        $selectBuilder = $this->createMock(SelectQueryBuilderContract::class);
        $deleteBuilder = $this->createMock(DeleteQueryBuilderContract::class);

        // Configure Select Builder
        $selectBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('orderBy')
            ->with('available_at')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('first')
            ->willReturn([
                'id' => '123',
                'payload' => new EncryptionService()->encrypt($this->job),
                'available_at' => '2024-01-01 12:00:00'
            ]);

        // Configure Delete Builder
        $deleteBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $deleteBuilder->expects($this->once())
            ->method('where')
            ->with('id', '=', '123')
            ->willReturnSelf();

        $deleteBuilder->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->app->set(SelectQueryBuilderContract::class, $selectBuilder);
        $this->app->set(DeleteQueryBuilderContract::class, $deleteBuilder);

        $queue = new DatabaseQueue();
        $job = $queue->pop();

        $this->assertInstanceOf(JobContract::class, $job);
    }

    public function testPopEmptyQueue(): void
    {
        // Mock Select Query Builder
        $selectBuilder = $this->createMock(SelectQueryBuilderContract::class);

        $selectBuilder->expects($this->once())
            ->method('table')
            ->with('jobs')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('select')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('orderBy')
            ->with('available_at')
            ->willReturnSelf();

        $selectBuilder->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $this->app->set(SelectQueryBuilderContract::class, $selectBuilder);

        $queue = new DatabaseQueue();
        $job = $queue->pop();

        $this->assertNull($job);
    }
}