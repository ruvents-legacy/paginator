# RUVENTS Paginator

## Description

This library provides a convenient way to paginate any data structures.

Some definitions:
- A `section` is a set of pages, rendered together.
- `Proximity` is a number of pages, displayed before and after the current page.

For example, for 10 pages with proximity 2 and current page 5 we will have 3 sections with the following page numbers: `[1], [3, 4, 5, 6, 7], [10]`.

When the current page is close to one of the edges sections get merged.


## Controller code example

```php
<?php

use Ruvents\Paginator\PaginatorBuilder;
use Ruvents\Paginator\Provider\IterableProvider;

$data = range(1, 100);

$paginator = PaginatorBuilder::create()
    // required
    ->setProvider(new IterableProvider($data))
    // defaults to 1
    // when out of range of estimated pages, CurrentPageOutOfRangeException is thrown
    ->setCurrent(2)
    // defaults to 2
    ->setProximity(1)
    // defaults to 10
    ->setPerPage(3)
    ->getPaginator();

// template logic

if ($previous = $paginator->getPrevious()) {
    echo sprintf('<a href="?page=%1$d">Previous</a>', $previous->getNumber());
}

foreach ($paginator as $section) {
    foreach ($section as $page) {
        echo sprintf('<a href="?page=%1$d" class="%2$s">%1$d</a>', $page->getNumber(), $page->isCurrent() ? 'active' : '');
    }
}

if ($next = $paginator->getNext()) {
    echo sprintf('<a href="?page=%1$d">Next</a>', $next->getNumber());
}
```

## Built-in data providers

### IterableProvider

Can be used with an `array` or an object implementing `\Traversable`.

### DoctrineOrmProvider

Can be used to paginate over Doctrine entities. Internally uses the native `Doctrine\ORM\Tools\Pagination\Paginator` helper.

```php
<?php

use Ruvents\Paginator\PaginatorBuilder;
use Ruvents\Paginator\Provider\DoctrineOrmProvider;
use Doctrine\ORM\EntityRepository;

/** @var EntityRepository $repository */

$qb = $repository->createQueryBuilder('entity')
    ->andWhere('entity.id = :id')
    ->setParameters([
        'id' => 1
    ]);

$paginator = PaginatorBuilder::create()
    ->setProvider(new DoctrineOrmProvider($qb))
    ->getPaginator();
```

### Custom

Create your own provider by implementing the `Ruvents\Paginator\Provider\ProviderInterface`.
