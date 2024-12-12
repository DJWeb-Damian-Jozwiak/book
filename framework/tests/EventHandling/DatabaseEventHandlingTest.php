<?php

namespace Tests\EventHandling;

use Carbon\Carbon;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Connection\MySqlConnection;
use DJWeb\Framework\Events\Database\QueryExecutedEvent;
use DJWeb\Framework\Events\Database\QueryLoggerListener;
use DJWeb\Framework\Events\EventManager;
use DJWeb\Framework\Web\Application;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;
use Tests\Helpers\Listeners\TestListener;

class DatabaseEventHandlingTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    public function testQuery(): void
    {
       // $this->mockConfig();
        $pdoMock = $this->createMock(PDO::class);
        $pdoStatementMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE id = ?')
            ->willReturn($pdoStatementMock);

        $pdoStatementMock->expects($this->once())
            ->method('execute')
            ->with([1]);

        $connection = $this->getMockBuilder(MySqlConnection::class)
            ->onlyMethods(['connectMysql', 'withEventManager'])
            ->getMock();
        $connection->eventManager = new EventManager();  // Dodaj to
        $connection->method('connectMysql')
            ->willReturn($pdoMock);
        $connection->method('withEventManager')->willReturnSelf();

        $connection->withEventManager(new EventManager());

        $path = Application::getInstance()->base_path;
        $path .= '/storage/logs/sql_queries.log';

        $now = Carbon::now();
        Carbon::setTestNow($now);
        $result = $connection->query(
            'SELECT * FROM users WHERE id = ?',
            [1]
        );
        $this->assertTrue(is_file($path));
        $content = file_get_contents($path);
        $this->assertStringContainsString($now->format('Y-m-d H:i:s.u'), $content);
        $this->assertSame($pdoStatementMock, $result);
    }

    private function mockApplication(): void
    {
        Application::withInstance(null);
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')->willReturnCallback(fn($key) => match ($key) {
            'events.listeners' => [
                QueryExecutedEvent::class => [
                    new QueryLoggerListener(),
                    function (QueryExecutedEvent $event) {
                        throw new \Exception('This should not be called');
                    }
                ]
            ],
            'database.mysql.host' => 'localhost',
            'database.mysql.port' => 3306,
            'database.mysql.database' => 'testdb',
            'database.mysql.username' => 'testuser',
            'database.mysql.password' => 'testpass',
            'database.mysql.charset' => 'utf8mb4',
        });
        $app->set(ConfigContract::class, $config);
        $app->bind('base_path', dirname(__DIR__));
    }
}