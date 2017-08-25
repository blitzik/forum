<?php declare(strict_types = 1);

namespace Topic\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Category\Category;
use Topic\Topic;
use Kdyby;

class TopicQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function byId(int $id): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($id) {
            $qb->andWhere('t.id = :id')->setParameter('id', $id);
        };

        return $this;
    }


    /**
     * @param Category|int $category
     * @return TopicQuery
     */
    public function byCategory($category): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($category) {
            $qb->andWhere('t.category = :category')->setParameter('category', $category);
        };

        return $this;
    }


    public function orderByDateOfCreation(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->orderBy('t.createdAt', $order);
        };

        return $this;
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('COUNT(t.id)');

        return $qb;
    }


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('t');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Topic::class, 't');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}