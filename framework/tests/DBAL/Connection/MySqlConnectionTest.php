<?php

namespace Tests\DBAL\Connection;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Connection\MySqlConnection;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MySqlConnectionTest extends TestCase
{
    private MySqlConnection|MockObject $connection;
    private $applicationMock;
    private $configMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = new MySqlConnection();
        $this->mockApplication();
    }

    private function mockApplication(): void
    {
        $this->applicationMock = $this->createMock(Application::class);
        $this->configMock = $this->createMock(ConfigContract::class);

        $this->applicationMock->method('getConfig')
            ->willReturn($this->configMock);

        Application::withInstance($this->applicationMock);
    }

    private function mockConfig(): void
    {
        $this->configMock->method('get')
            ->willReturnMap([
                ['database.mysql.host', null, 'localhost'],
                ['database.mysql.port', null, 3306],
                ['database.mysql.database', null, 'testdb'],
                ['database.mysql.username', null, 'testuser'],
                ['database.mysql.password', null, 'testpass'],
                ['database.mysql.charset', 'utf8mb4', 'utf8mb4'],
            ]);
    }

    public function testConnect(): void
    {
        $this->mockConfig();
        $pdoMock = $this->createMock(PDO::class);

        $this->connection = $this->getMockBuilder(MySqlConnection::class)
            ->onlyMethods(['connectMysql'])
            ->getMock();
        $this->connection->expects($this->once())
            ->method('connectMysql')
            ->willReturn($pdoMock);

        $this->connection->connect();
        $pdo = $this->connection->getConnection();

        // Call connect again to ensure it doesn't try to reconnect
        // and return the same instance of PDO
        $this->connection->connect();
        $pdo2 = $this->connection->getConnection();
        $this->assertSame($pdo, $pdo2);
    }

    public function testDisconnect(): void
    {
        $this->mockConfig();
        $pdoMock = $this->createMock(PDO::class);

        $this->connection = $this->getMockBuilder(MySqlConnection::class)
            ->onlyMethods(['connectMysql'])
            ->getMock();
        $this->connection->method('connectMysql')
            ->willReturn($pdoMock);

        $this->connection->connect();
        $this->connection->disconnect();
        $this->assertNull($this->connection->getConnection());

        // Reconnect to ensure disconnect worked
        $this->connection->connect();
    }

    public function testQuery(): void
    {
        $this->mockConfig();
        $pdoMock = $this->createMock(PDO::class);
        $pdoStatementMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE id = ?')
            ->willReturn($pdoStatementMock);

        $pdoStatementMock->expects($this->once())
            ->method('execute')
            ->with([1]);

        $this->connection = $this->getMockBuilder(MySqlConnection::class)
            ->onlyMethods(['connectMysql'])
            ->getMock();
        $this->connection->method('connectMysql')
            ->willReturn($pdoMock);

        $result = $this->connection->query(
            'SELECT * FROM users WHERE id = ?',
            [1]
        );

        $this->assertSame($pdoStatementMock, $result);
    }

    public function testConnectMysqlThrowsException(): void
    {
        $connection = new MySqlConnection();
        $this->expectException(PDOException::class);
        $connection->connect();
    }

    private function invokeMethod(
        $object,
        string $methodName,
        array $parameters = []
    ) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}