<?php declare(strict_types = 1);

namespace Category\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Category\Section;
use Kdyby;

class SectionQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function onlyPublic(): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->andWhere('s.isPublic = true');
        };

        return $this;
    }


    public function orderByPosition(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->orderBy('s.position', $order);
        };

        return $this;
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('COUNT(s.id)');

        return $qb;
    }


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('s');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Section::class, 's');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}