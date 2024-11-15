<?php

declare(strict_types=1);

namespace DJWeb\Framework\Validation;

readonly class CompareCondition
{
    public function __construct(private int|float $value) {
    }

    final public function compare(string $operator, int|float $value): bool
    {
        return $this->maps()[$operator]($value) ?? false;
    }

    /**
     * @return array<string, \Closure>
     */
    protected function maps(): array
    {
        return [
            '>' => $this->bigger(...),
            '<' => $this->smaller(...),
            '=' => $this->equals(...),
            '!=' => $this->notEquals(...),
            '>=' => $this->biggerOrEquals(...),
            '<=' => $this->smallerOrEquals(...),
        ];
    }

    private function bigger(int|float $value): bool
    {
        return $this->value > $value;
    }

    private function smaller(int|float $value): bool
    {
        return $this->value < $value;
    }

    private function equals(int|float $value): bool
    {
        return $this->value === $value;
    }

    private function notEquals(int|float $value): bool
    {
        return $this->value !== $value;
    }

    private function biggerOrEquals(int|float $value): bool
    {
        return $this->value >= $value;
    }

    private function smallerOrEquals(int|float $value): bool
    {
        return $this->value <= $value;
    }

}
