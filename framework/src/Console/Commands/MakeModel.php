<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Commands;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Attributes\CommandOption;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Schema\Column;

#[AsCommand('make:model')]
class MakeModel extends MakeCommand
{
    #[CommandOption('table', required: true)]
    protected string $table = '';
    private DatabaseInfoContract $databaseInfo;

    public function __construct(
        ContainerContract $container,
    )
    {
        parent::__construct($container);
    }

    protected function getDefaultNamespace(): string
    {
        return $this->rootNamespace() . 'Database\\Models';
    }

    protected function getPath(string $name): string
    {
        $name = str_replace('\\', '/', $name);
        return $this->container->getBinding(
            'app.models_path'
        ) . '/' . $name;
    }

    protected function buildClass(string $name): string
    {
        $stub = parent::buildClass($name);
        $this->databaseInfo = $this->container->get(DatabaseInfoContract::class);
        $columns = $this->databaseInfo->getColumns($this->table);
        $stub = $this->addColumnProperties($stub, $columns);
        $stub = $this->addCasts($stub, $columns);
        return $this->addTableProperty($stub);
    }

    protected function getStub(): string
    {
        $dir = dirname(__DIR__, 3);
        return $dir . '/stubs/model.stub';
    }

    private function addColumnProperties(string $stub, array $columns): string
    {
        $properties = '';
        foreach ($columns as $column) {
            $properties .= $this->generateColumnProperty($column);

        }
        return str_replace('// DummyColumnProperties', $properties, $stub);
    }

    private function generateColumnProperty(Column $column): string
    {
        $type = $column->getSqlColumn();
        if ($column->name === 'id') {
            return '';

        }
        return <<<DEFINITION
    public {$type} \${$column->name} {
        get => \$this->{$column->name};
        set {
            \$this->{$column->name} = \$value;
            \$this->markPropertyAsChanged('{$column->name}');
        }
    }
DEFINITION;
    }

    private function addTableProperty(string $stub): string
    {
        return str_replace('DummyTable', $this->table, $stub);
    }

    private function addCasts(string $stub, array $columns): string
    {
        $casts = [];
        $columns = array_filter($columns, static fn (Column $column) => $column->shouldBeCasted());
        foreach ($columns as $column) {
            $casts[$column->name] = $this->getCastType($column);
        }

        if ($casts) {
            $items = implode(
                ",\n        ",
                array_map(static fn ($k, $v) => "'{$k}' => '{$v}'", array_keys($casts), $casts)
            );
            $castsProperty = <<<CASTS
    protected array \$casts = [
        {$items}
    ];
CASTS;
            $stub = str_replace('// DummyCasts', $castsProperty, $stub);
        }

        return $stub;
    }

    private function getCastType(Column $column)
    {
        $casts = [
            'DATETIME' => 'datetime',
        ];
        return $casts[$column->type] ?? 'string';
    }

}
