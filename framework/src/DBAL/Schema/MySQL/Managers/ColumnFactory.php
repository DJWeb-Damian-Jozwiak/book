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
    /**
     * @param array<string, mixed> $description
     */
    public function createFromDescription(array $description): Column
    {
        $isInt = preg_match('/^int/i', $description['Type']);
        $isVarchar = preg_match('/^varchar/i', $description['Type']);
        $isText = $description['Type'] === 'text';
        $isDatetime = preg_match('/^datetime/i', $description['Type']);
        $isEnum = preg_match('/^enum/i', $description['Type']);
        return match (true) {
            (bool) $isInt => $this->createIntColumn($description),
            (bool) $isVarchar => $this->createVarcharColumn($description),
            $isText => $this->createTextColumn($description),
            (bool) $isDatetime => $this->createDateTimeColumn($description),
            (bool) $isEnum => $this->createEnumColumn($description),
            default => throw new UnsupportedColumnType($description['Type']),
        };
    }

    /**
     * @param array<string, mixed> $description
     */
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

    /**
     * @param array<string, mixed> $description
     */
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

    /**
     * @param array<string, mixed> $description
     */
    private function createTextColumn(array $description): TextColumn
    {
        return new TextColumn(
            $description['Field'],
            $description['Null'] === 'YES'
        );
    }

    /**
     * @param array<string, mixed> $description
     */
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

    /**
     * @param array<string, mixed> $description
     */
    private function createEnumColumn(array $description): EnumColumn
    {
        preg_match('/enum\((.*)\)/i', $description['Type'], $matches);
        $values = str_getcsv($matches[1] ?? '');
        $values = array_filter(
            $values,
            static fn (string|null $value) => (bool) $value
        );
        $values = array_map(
            static fn (string $value) => str_replace("'", '', $value),
            $values
        );
        return new EnumColumn(
            $description['Field'],
            $values,
            $description['Null'] === 'YES',
            $description['Default']
        );
    }
}
