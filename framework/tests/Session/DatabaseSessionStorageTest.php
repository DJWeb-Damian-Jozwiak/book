<?php

namespace Tests\Session;

use Carbon\Carbon;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Entities\Session;
use DJWeb\Framework\Encryption\EncryptionService;
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
        $keyEncryption = new KeyGenerator()->generateKey();
        $config->method('get')->willReturnCallback(fn(string $key) => match ($key) {
            'app.key' => $keyEncryption,
            'session.cookie_params.lifetime' => 1000,
            default => null,
        });
        $this->app->set(ConfigContract::class, $config);
        // Mock dla query buildera
        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);

        $builder->method('table')->willReturnSelf();
        $builder->method('values')->willReturnSelf();
        $builder->method('execute')->willReturn($stmt);
        $builder->method('getInsertId')->willReturn('1');

//        $select = $this->createMock(SelectQueryBuilderContract::class);
//        $this->app->set(SelectQueryBuilderContract::class, $select);
//        $delete = $this->createMock(DeleteQueryBuilderContract::class);
//        $this->app->set(DeleteQueryBuilderContract::class, $delete);

        $this->app->set(InsertQueryBuilderContract::class, $builder);

        $this->security = new SessionSecurity();
        $this->storage = new DatabaseSessionHandler($this->security);
    }

    public function testOpenClose()
    {
        $this->assertTrue( $this->storage->open( 'test', 'test' ) );
        $this->assertTrue( $this->storage->close() );
    }

    public function testCanWriteAndReadSession(): void
    {
        $id = 'test_session';
        $data = ['user_id' => 1, 'name' => 'Test'];

        $select = $this->createMock(SelectQueryBuilderContract::class);
        $any = $this->any();
        $select->expects($any)->method('select')->willReturnSelf();
        $select->expects($any)->method('table')->willReturnSelf();
        $select->expects($any)->method('where')->willReturnSelf();
        $select->expects($any)->method('first')->willReturn(
            [
                'last_activity' => Carbon::now()->subMinute()->getTimestamp(),
                'payload' => new EncryptionService()->encrypt(json_encode($data))
            ]
        );
        $this->app->set(SelectQueryBuilderContract::class, $select);

        $this->storage->write($id, json_encode($data));
        $result = $this->storage->read($id);

        $this->assertEquals(json_encode($data), $result);
    }

    public function testReturnsEmptyArrayForNonexistentSession(): void
    {
        $select = $this->createMock(SelectQueryBuilderContract::class);
        $select->expects($this->once())->method('select')->willReturnSelf();
        $select->expects($this->once())->method('table')->willReturnSelf();
        $select->expects($this->once())->method('where')->willReturnSelf();
        $select->expects($this->once())->method('first')->willReturn(null);
        $this->app->set(SelectQueryBuilderContract::class, $select);
        $result = $this->storage->read('nonexistent');
        $this->assertEquals('', $result);
    }

    public function testCanDestroySession(): void
    {
        $id = 'test_session';
        $data = ['test' => 'data'];
        $any = $this->any();
        $select = $this->createMock(SelectQueryBuilderContract::class);
        $select->expects($any)->method('select')->willReturnSelf();
        $select->expects($any)->method('table')->willReturnSelf();
        $select->expects($any)->method('where')->willReturnSelf();
        $select->expects($any)->method('first')->willReturn(null);
        $this->app->set(SelectQueryBuilderContract::class, $select);
        $delete = $this->createMock(DeleteQueryBuilderContract::class);
        $this->app->set(DeleteQueryBuilderContract::class, $delete);

        $this->storage->write($id, json_encode($data));
        $this->storage->destroy($id);
        $this->storage->gc(1800);

        $result = $this->storage->read($id);

        $this->assertEquals('', $result);
    }

    public function testCanReadPastSession(): void
    {
        $id = 'test_session';
        $data = ['test' => 'data'];
        $any = $this->any();
        $select = $this->createMock(SelectQueryBuilderContract::class);
        $select->expects($any)->method('select')->willReturnSelf();
        $select->expects($any)->method('table')->willReturnSelf();
        $select->expects($any)->method('where')->willReturnSelf();
        $select->expects($any)->method('first')->willReturn([
            'last_activity' => Carbon::now()->subHour()->getTimestamp(),
            'payload' => new EncryptionService()->encrypt(json_encode($data))
        ]);
        $this->app->set(SelectQueryBuilderContract::class, $select);
        $delete = $this->createMock(DeleteQueryBuilderContract::class);
        $this->app->set(DeleteQueryBuilderContract::class, $delete);

        $this->storage->write($id, json_encode($data));
        $result = $this->storage->read($id);

        $this->assertEquals('', $result);
    }

}