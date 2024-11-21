<?php

namespace Tests\Session;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Entities\Session;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Storage\Session\Handlers\DatabaseSessionHandler;
use DJWeb\Framework\Storage\Session\SessionSecurity;
use DJWeb\Framework\Web\Application;
use PDOStatement;
use Tests\BaseTestCase;

class DatabaseSessionStorageTest extends BaseTestCase
{
    private Application $app;
    private DatabaseSessionHandler $storage;
    private SessionSecurity $security;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = Application::getInstance();

        // Mock dla config
        $config = $this->createMock(ConfigContract::class);
        $config->method('get')->willReturn('base64:'.base64_encode(random_bytes(32)));
        $this->app->set(ConfigContract::class, $config);
        $config = $this->createMock(ConfigContract::class);
        $config->method('get')->willReturn(new KeyGenerator()->generateKey());
        $this->app->set(ConfigContract::class, $config);
        // Mock dla query buildera
        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);

        $builder->method('table')->willReturnSelf();
        $builder->method('values')->willReturnSelf();
        $builder->method('execute')->willReturn($stmt);
        $builder->method('getInsertId')->willReturn('1');

        $select = $this->createMock(SelectQueryBuilderContract::class);
        $this->app->set(SelectQueryBuilderContract::class, $select);
        $delete = $this->createMock(DeleteQueryBuilderContract::class);
        $this->app->set(DeleteQueryBuilderContract::class, $delete);

        $this->app->set(InsertQueryBuilderContract::class, $builder);

        $this->security = new SessionSecurity();
        $this->storage = new DatabaseSessionHandler($this->security);
    }

    public function testOpenClose()
    {
        $this->assertTrue( $this->storage->open( 'test', 'test' ) );
        $this->assertTrue( $this->storage->close() );
    }

    public function testReturnsEmptyArrayForNonexistentSession(): void
    {
        $result = $this->storage->read('nonexistent');
        $this->assertEquals('', $result);
    }

    public function testCanDestroySession(): void
    {
        $id = 'test_session';
        $data = ['test' => 'data'];

        $this->storage->write($id, json_encode($data));
        $this->storage->destroy($id);

        $result = $this->storage->read($id);
        $this->assertEquals('', $result);
    }

}