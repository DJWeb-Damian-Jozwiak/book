<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\Schema\ColumnFactoryContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\DateTimeColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\EnumColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\IntColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\TextColumn;
use DJWeb\Framework\DBAL\Schema\MySQL\Columns\VarcharColumn;
use DJWeb\Framework\Exceptions\DBAL\Schema\UnsupportedColumnType;

class ColumnFactory implements ColumnFactoryContract
{
    public function createFromDescription(array $description): Column
    {
        return match (true) {
            (bool) preg_match(
                '/^int/i',
                $description['Type']
            ) => $this->createIntColumn($description),
            (bool) preg_match(
                '/^varchar/i',
                $description['Type']
            ) => $this->createVarcharColumn($description),
            $description['Type'] === 'text' => $this->createTextColumn(
                $description
            ),
            (bool) preg_match(
                '/^datetime/i',
                $description['Type']
            ) => $this->createDateTimeColumn($description),
            (bool) preg_match(
                '/^enum/i',
                $description['Type']
            ) => $this->createEnumColumn($description),
            default => throw new UnsupportedColumnType($description['Type']),
        };
    }

    private function createIntColumn(array $description): IntColumn
    {
        preg_match('/int\((\d+)\)/i', $description['Type'], $matches);
        $length = $matches[1] ?? 11;
        $unsigned = str_contains($description['Type'], 'unsigned');
        $autoIncrement = $description['Extra'] === 'auto_increment';
        return new IntColumn(
            $description['Field'],
            $description['Null'] === 'YES',
            $description['Default'],
            (int) $length,
            $unsigned,
            $autoIncrement
        );
    }

    private function createVarcharColumn(array $description): VarcharColumn
    {
        preg_match('/varchar\((\d+)\)/i', $description['Type'], $matches);
        $length = $matches[1] ?? 255;
        return new VarcharColumn(
            $description['Field'],
            $description['Null'] === 'YES',
            $description['Default'],
            (int) $length,
        );
    }

    private function createTextColumn(array $description): TextColumn
    {
        return new TextColumn(
            $description['Field'],
            $description['Null'] === 'YES'
        );
    }

    private function createDateTimeColumn(array $description): DateTimeColumn
    {
        $current = $description['Default'] === 'CURRENT_TIMESTAMP';
        $currentOnUpdate = str_contains(
            $description['Extra'],
            'on update CURRENT_TIMESTAMP'
        );
        return new DateTimeColumn(
            $description['Field'],
            $description['Null'] === 'YES',
            $description['Default'],
            $current,
            $currentOnUpdate
        );
    }

    private function createEnumColumn(array $description): EnumColumn
    {
        preg_match('/enum\((.*)\)/i', $description['Type'], $matches);
        $values = str_getcsv(str_replace("'", '', $matches[1]));
        return new EnumColumn(
            $description['Field'],
            $values,
            $description['Null'] === 'YES',
            $description['Default']
        );
    }
}
