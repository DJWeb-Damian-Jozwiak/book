<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\EnumColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\PrimaryColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\TextColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use Tests\BaseTestCase;

class MakeModelTest extends BaseTestCase
{
    private Application $app;
    private OutputContract $output;

    private DatabaseInfoContract $databaseInfo;

    public function testMakeModel()
    {
        $factoryName = 'Car';
        $file = 'Car.php';
        $_SERVER['argv'] = ['console/bin', 'make:model', $factoryName];

        $this->app->set(OutputContract::class, $this->output);
        $this->databaseInfo->expects($this->once())->method('getColumns')->willReturn([
            new PrimaryColumn(),
            new VarcharColumn('brand'),
            new DateTimeColumn('production_date'),
            new EnumColumn('color', ['red', 'blue', 'green']),
            new IntColumn('seats'),
            new TextColumn('description'),
        ]);
        $this->app->set(DatabaseInfoContract::class, $this->databaseInfo);

        $this->output->expects($this->once())
            ->method('info')
            ->with("Utworzono {$file}");

        $result = $this->app->handle();

        $this->assertEquals(0, $result);
        $this->assertFileExists(sys_get_temp_dir() . '/' . $file);

        // Sprawdzenie zawartości pliku
        $content = file_get_contents(sys_get_temp_dir() . '/' . $file);
        $this->assertStringContainsString('class Car extends Model', $content);
        $this->assertStringContainsString('get => $this->brand;', $content);
        $this->assertStringContainsString('get => $this->brand;', $content);
        $this->assertStringContainsString(' $this->markPropertyAsChanged(\'brand\')', $content);
    }
    public function setUp(): void
    {
        Application::withInstance(null);
        $this->app = Application::getInstance();
        $this->app->bind('app.root_namespace', 'Tests\\Helpers\\');
        $this->app->bind('app.factories_namespace', 'Models\\');
        $this->app->bind(
            'app.base_path',
            sys_get_temp_dir()
        );
        $this->app->bind('app.models_path', sys_get_temp_dir());
        $this->output = $this->createMock(OutputContract::class);
        $this->databaseInfo = $this->createMock(DatabaseInfoContract::class);
        $this->app->set(ContainerContract::class, $this->app);

    }
}