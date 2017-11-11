<?php
declare(strict_types=1);

namespace Ruvents\Paginator;

use Ruvents\Paginator\Exception\PageOutOfRangeException;
use Ruvents\Paginator\Provider\ProviderInterface;

class PaginatorBuilder
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    private $current = 1;

    private $perPage = 10;

    private $proximity = 2;

    public static function create()
    {
        return new static();
    }

    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    public function setCurrent(int $current)
    {
        $this->current = $current;

        return $this;
    }

    public function setPerPage(int $perPage)
    {
        if ($perPage < 1) {
            throw new \InvalidArgumentException(sprintf('The $perPage value must be a positive integer.'));
        }

        $this->perPage = $perPage;

        return $this;
    }

    public function setProximity(int $proximity)
    {
        if ($proximity < 1) {
            throw new \InvalidArgumentException(sprintf('The $proximity value must be a positive integer.'));
        }

        $this->proximity = $proximity;

        return $this;
    }

    public function getPaginator(): Paginator
    {
        if (null === $this->provider) {
            throw new \LogicException('Provider is not set.');
        }

        $totalItems = $this->provider->getTotal();
        $total = $this->calculateTotal($totalItems);

        if ($this->current < 1 || $this->current > $total) {
            throw new PageOutOfRangeException($total, $this->current);
        }

        $items = $this->provider->getItems(($this->current - 1) * $this->perPage, $this->perPage);
        $sections = $this->buildSections($total);

        return new Paginator($sections, $total, $items, $totalItems, $this->current);
    }

    private function calculateTotal(int $totalItems): int
    {
        return (int)ceil($totalItems / $this->perPage) ?: 1;
    }

    private function buildSections(int $total): array
    {
        $sections = [];

        // calculate the section of the first page
        $lastLeftPoint = $lastRightPoint = 1;

        // calculate the section of the current page
        $leftPoint = $this->current - $this->proximity;

        if ($lastRightPoint + 1 < $leftPoint) {
            // add the section of the first page
            $sections[] = $this->buildSection($total, $lastLeftPoint, $lastRightPoint);
            $lastLeftPoint = $leftPoint;
        }

        $lastRightPoint = $this->current + $this->proximity;

        // calculate the section of the last page
        if ($lastRightPoint + 1 < $total) {
            // add the section of the current page
            $sections[] = $this->buildSection($total, $lastLeftPoint, $lastRightPoint);
            $lastLeftPoint = $total;
        }

        // add the section of the last page
        $sections[] = $this->buildSection($total, $lastLeftPoint, $total);

        return $sections;
    }

    private function buildSection(int $total, int $first, int $last): Section
    {
        $pages = [];

        for ($number = $first; $number <= $last; $number++) {
            $pages[] = new Page($number, 1 === $number, $total === $number, $this->current === $number);
        }

        return new Section($pages);
    }
}
