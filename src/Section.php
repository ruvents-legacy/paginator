<?php
declare(strict_types=1);

namespace Ruvents\Paginator;

final class Section implements \IteratorAggregate, \Countable
{
    private $pages;

    /**
     * @param Page[] $pages
     */
    public function __construct(array $pages)
    {
        $this->pages = $pages;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Traversable|Page[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->pages);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->pages);
    }
}
