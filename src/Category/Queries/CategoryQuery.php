<?php declare(strict_types = 1);

namespace Category\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Category\Category;
use Kdyby;

class CategoryQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function byId(int $id): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('c.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    public function withSection(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->addSelect('s')
               ->join('c.section', 's');
        };

        return $this;
    }


    public function withLastPost(): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->addSelect(['lp', 'a'])
               ->leftJoin('c.lastPost', 'lp')
               ->leftJoin('lp.author', 'a');
        };

        return $this;
    }


    public function onlyPublic(): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) {
            $qb->andWhere('c.isPublic = true');
        };

        return $this;
    }


    public function orderByPosition(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->orderBy('c.position', $order);
        };

        return $this;
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('COUNT(c.id)');

        return $qb;
    }


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('c');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Category::class, 'c');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}