<?php

namespace Ruvents\Paginator;

use PHPUnit\Framework\TestCase;
use Ruvents\Paginator\Provider\ProviderInterface;

class PaginatorBuilderTest extends TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testNoProvider()
    {
        PaginatorBuilder::create()->getPaginator();
    }

    /**
     * @dataProvider getNonPositiveIntegers
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPerPageException(int $value)
    {
        PaginatorBuilder::create()->setPerPage($value);
    }

    /**
     * @dataProvider getNonPositiveIntegers
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidProximityException(int $value)
    {
        PaginatorBuilder::create()->setProximity($value);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testUnexpectedProviderTotalException()
    {
        $provider = $this->createMock(ProviderInterface::class);

        $provider->expects($this->once())
            ->method('getTotal')
            ->willReturn(-1);

        /* @var ProviderInterface $provider */

        PaginatorBuilder::create()
            ->setProvider($provider)
            ->getPaginator();
    }

    /**
     * @expectedException \Ruvents\Paginator\Exception\PageOutOfRangeException
     * @expectedExceptionMessage Page 2 is out of range [1, 1].
     */
    public function testCurrentPageOutOfRangeException()
    {
        $provider = $this->createMock(ProviderInterface::class);

        $provider->expects($this->once())
            ->method('getTotal')
            ->willReturn(1);

        /* @var ProviderInterface $provider */

        PaginatorBuilder::create()
            ->setProvider($provider)
            ->setCurrent(2)
            ->getPaginator();
    }

    public function getNonPositiveIntegers()
    {
        return [
            [-100],
            [-2],
            [0],
        ];
    }
}
