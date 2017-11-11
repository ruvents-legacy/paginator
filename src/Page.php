<?php
declare(strict_types=1);

namespace Ruvents\Paginator;

final class Page
{
    private $number;

    private $first;

    private $last;

    private $current;

    public function __construct(int $number, bool $first, bool $last, bool $current)
    {
        $this->number = $number;
        $this->first = $first;
        $this->last = $last;
        $this->current = $current;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function isFirst(): bool
    {
        return $this->first;
    }

    public function isLast(): bool
    {
        return $this->last;
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }
}
