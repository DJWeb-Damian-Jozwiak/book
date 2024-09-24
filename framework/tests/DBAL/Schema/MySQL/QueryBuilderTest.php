<?php

namespace Tests\DBAL\Schema\MySQL;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use DJWeb\Framework\DBAL\Query\Conditions\WhereGroupCondition;
use Tests\BaseTestCase;

class QueryBuilderTest extends BaseTestCase
{
    private QueryBuilder $queryBuilder;
    private $mockConnection;

    public function testSelect()
    {
        $query = $this->queryBuilder->select('users');
        $this->assertEquals('SELECT * FROM users', $query->getSQL());
    }

    public function testLimitOffset()
    {
        $query = $this->queryBuilder->select('users')
            ->limit(10)->offset(10);
        $this->assertEquals(
            'SELECT * FROM users LIMIT 10 OFFSET 10',
            $query->getSQL()
        );
    }

    public function testSelectColumns()
    {
        $query = $this->queryBuilder->select('users')
            ->select(['name', 'email']);
        $this->assertEquals('SELECT name, email FROM users', $query->getSQL());
    }

    public function testWhere()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        );
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ?',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com'], $query->getParams());
    }

    public function testAndWhere()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->andWhere('age', '>', 20);
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? AND age > ?',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com', 20], $query->getParams());
    }

    public function testAndNull()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->whereNull('name');
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? AND name IS NULL',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com'], $query->getParams());
    }

    public function testAndNotNull()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->whereNotNull('name', false);
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? OR name IS NOT NULL',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com'], $query->getParams());
    }

    public function testWhereGroup()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->whereGroup(function (WhereGroupCondition $condition) {
            $condition->whereLike('name', 'john%')
                ->orWhere('age', '>', 20);
        });
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? AND (name LIKE ? OR age > ?)',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com', 'john%', 20], $query->getParams());
    }

    public function testWhereGroupWithAnd()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->whereGroup(function (WhereGroupCondition $condition) {
            $condition->whereLike('name', 'john%')
                ->andWhere('age', '>', 20);
        });
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? AND (name LIKE ? AND age > ?)',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com', 'john%', 20], $query->getParams());
    }

    public function testSelectFirst()
    {
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'age' => 30,
                    'status' => 'active'
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $result = $this->queryBuilder->select('users')
            ->first();

        $this->assertIsArray($result);
        $this->assertEquals('John Doe', $result['name']);
    }

    public function testWhereGroupNull()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->whereGroup(function (WhereGroupCondition $condition) {
            $condition->whereNull('banned')
                ->whereNotNull('first_name');
        });
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? AND (banned IS NULL AND first_name IS NOT NULL)',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com'], $query->getParams());
    }

    public function testOrWhere()
    {
        $query = $this->queryBuilder->select('users')->where(
            'email',
            '=',
            'john@doe.com'
        )->orWhere('age', '>', 20);
        $this->assertEquals(
            'SELECT * FROM users WHERE email = ? OR age > ?',
            $query->getSQL()
        );
        $this->assertEquals(['john@doe.com', 20], $query->getParams());
    }

    public function testInsert()
    {
        $query = $this->queryBuilder->insert('users')
            ->values(['name' => 'John Doe', 'age' => 30]);

        $this->assertEquals(
            'INSERT INTO users (name, age) VALUES (?, ?)',
            $query->getSQL()
        );
    }

    public function testUpdate()
    {
        $query = $this->queryBuilder->update('users')
            ->set(['name' => 'John Doe', 'age' => 30]);

        $this->assertEquals(
            'UPDATE users SET name = ?, age = ? ',
            $query->getSQL()
        );
        $this->assertEquals(['John Doe', '30'], $query->getParams());
    }

    public function testDelete()
    {
        $query = $this->queryBuilder->delete('users')
            ->where('verified', '=', 0);

        $this->assertEquals(
            'DELETE FROM users WHERE verified = ?',
            $query->getSQL()
        );
        $this->assertEquals([0], $query->getParams());
    }

    public function testLeftJoin()
    {
        $query = $this->queryBuilder->select('users')
            ->leftJoin('books', 'users.id', '=', 'books.author')
            ->where('users.id', '>', 20);
        $this->assertEquals(
            'SELECT * FROM users SELECT * FROM users LEFT JOIN books ON users.id = books.author WHERE users.id > ?',
            $query->getSQL()
        );
    }

    public function testRightJoin()
    {
        $query = $this->queryBuilder->select('users')
            ->rightJoin('books', 'users.id', '=', 'books.author')
            ->where('users.id', '>', 20);
        $this->assertEquals(
            'SELECT * FROM users SELECT * FROM users RIGHT JOIN books ON users.id = books.author WHERE users.id > ?',
            $query->getSQL()
        );
    }

    public function testInnerJoin()
    {
        $query = $this->queryBuilder->select('users')
            ->innerJoin('books', 'users.id', '=', 'books.author')
            ->where('users.id', '>', 20);
        $this->assertEquals(
            'SELECT * FROM users SELECT * FROM users INNER JOIN books ON users.id = books.author WHERE users.id > ?',
            $query->getSQL()
        );
    }

    protected function setUp(): void
    {
        $this->mockConnection = $this->createMock(ConnectionContract::class);
        Application::getInstance()->set(
            InsertQueryBuilder::class,
            new InsertQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            UpdateQueryBuilder::class,
            new UpdateQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            DeleteQueryBuilder::class,
            new DeleteQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            SelectQueryBuilder::class,
            new SelectQueryBuilder($this->mockConnection)
        );
        $this->queryBuilder = new QueryBuilder($this->mockConnection);
    }
}