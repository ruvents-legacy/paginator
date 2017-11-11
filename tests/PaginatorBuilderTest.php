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
    public function testSetInvalidPerPage(int $value)
    {
        PaginatorBuilder::create()->setPerPage($value);
    }

    /**
     * @dataProvider getNonPositiveIntegers
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidProximity(int $value)
    {
        PaginatorBuilder::create()->setProximity($value);
    }

    /**
     * @expectedException \Ruvents\Paginator\Exception\CurrentPageOutOfRangeException
     * @expectedExceptionMessage Page 2 is out of range [1, 1].
     */
    public function testSetInvalidCurrent()
    {
        $provider = $this->getMockBuilder(ProviderInterface::class)
            ->setMethods(['getTotal', 'getItems'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getTotal')
            ->willReturn(1);

        $provider->expects($this->never())
            ->method('getItems');

        /** @var ProviderInterface $provider */

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
