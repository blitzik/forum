<?php declare(strict_types = 1);

namespace Post\Queries;

use Kdyby\Persistence\Queryable;
use Kdyby\Doctrine\QueryObject;
use Post\Post;
use Kdyby;

class PostQuery extends QueryObject
{
    /** @var array */
    private $select = [];

    /** @var array  */
    private $filter = [];


    public function withAuthor(array $fields = []): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($fields) {
            if (!empty($fields)) {
                $fields[] = 'id';
                $fields = array_unique($fields);
                $qb->addSelect(sprintf('PARTIAL a.{%s}', implode(',', $fields)));

            } else {
                $qb->addSelect('a');
            }
            $qb->innerJoin('p.author', 'a');
        };

        return $this;
    }


    public function withAuthorRole(array $fields = []): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($fields) {
            if (!empty($fields)) {
                $fields[] = 'id';
                $fields = array_unique($fields);
                $qb->addSelect(sprintf('PARTIAL r.{%s}', implode(',', $fields)));

            } else {
                $qb->addSelect('r');
            }
            $qb->innerJoin('a.role', 'r');
        };

        return $this;
    }


    public function byTopic($topic): self
    {
        $this->filter[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($topic) {
            $qb->andWhere('p.topic = :topic')->setParameter('topic', $topic);
        };

        return $this;
    }


    public function orderedByCreationTime(string $order = 'ASC'): self
    {
        $this->select[] = function (Kdyby\Doctrine\QueryBuilder $qb) use ($order) {
            $qb->orderBy('p.createdAt', $order);
        };

        return $this;
    }


    protected function doCreateCountQuery(Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('COUNT(p.id)');

        return $qb;
    }


    protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
    {
        $qb = $this->createBasicQuery($repository);
        $qb->select('p');

        foreach ($this->select as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }


    private function createBasicQuery(Kdyby\Persistence\Queryable $repository)
    {
        /** @var Kdyby\Doctrine\QueryBuilder $qb */
        $qb = $repository->getEntityManager()->createQueryBuilder();
        $qb->from(Post::class, 'p');

        foreach ($this->filter as $modifier) {
            $modifier($qb);
        }

        return $qb;
    }

}