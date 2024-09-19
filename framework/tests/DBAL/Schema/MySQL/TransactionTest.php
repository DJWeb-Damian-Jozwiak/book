<?php

namespace Tests\DBAL\Schema\MySQL;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Transaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    private Transaction $transaction;
    private MockObject $connectionMock;

    public function testBegin()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('START TRANSACTION')
            ->willReturn($pdoStatementMock);

        $this->transaction->begin();
    }

    public function testCommit()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('COMMIT')
            ->willReturn($pdoStatementMock);

        $this->transaction->commit();
    }

    public function testRollback()
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->connectionMock->expects($this->once())
            ->method('query')
            ->with('ROLLBACK')
            ->willReturn($pdoStatementMock);

        $this->transaction->rollback();
    }

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(ConnectionContract::class);
        $this->transaction = new Transaction($this->connectionMock);
    }
}