<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers\Builders;

use DJWeb\Framework\DBAL\Schema\Column;

class AlterTableBuilder
{
    /**
     * @param array<int, mixed> $modifications
     */
    public function build(string $tableName, array $modifications): string
    {
        $alterStatements = [];
        $alterStatements = $this->addStatements(
            $modifications,
            $alterStatements
        );
        $alterStatements = $this->changeStatements(
            $modifications,
            $alterStatements
        );
        $alterStatements = $this->dropStatements(
            $modifications,
            $alterStatements
        );
        return "ALTER TABLE {$tableName} " . implode(', ', $alterStatements);
    }

    /**
     * @param array<int, mixed> $modifications
     * @param array<int, string> $alterStatements
     *
     * @return array<int, string>
     */
    public function addStatements(
        array $modifications,
        array $alterStatements
    ): array {
        $addStatements = array_filter(
            $modifications,
            static fn ($modification) => $modification instanceof Column
        );
        foreach ($addStatements as $addStatement) {
            $alterStatements[] = 'ADD COLUMN ' .
                $addStatement->getSqlDefinition();
        }
        return $alterStatements;
    }

    /**
     * @param array<int, mixed> $modifications
     * @param array<int, string> $alterStatements
     *
     * @return array<int, string>
     */
    public function changeStatements(
        array $modifications,
        array $alterStatements
    ): array {
        $changeStatements = array_filter(
            $modifications,
            static fn ($modification) => is_array($modification)
                && count($modification) === 2
        );
        foreach ($changeStatements as $changeStatement) {
            $alterStatements[] = sprintf(
                'CHANGE COLUMN %s %s',
                $changeStatement[0],
                $changeStatement[1]->getSqlDefinition()
            );
        }
        return $alterStatements;
    }

    /**
     * @param array<int, mixed> $modifications
     * @param array<int, string> $alterStatements
     *
     * @return array<int, string>
     */
    public function dropStatements(
        array $modifications,
        array $alterStatements
    ): array {
        $dropStatements = array_filter(
            $modifications,
            static fn ($modification) => is_string($modification)
        );
        foreach ($dropStatements as $dropStatement) {
            $alterStatements[] = "DROP COLUMN {$dropStatement}";
        }
        return $alterStatements;
    }
}
