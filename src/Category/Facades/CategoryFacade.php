<?php declare(strict_types = 1);

namespace Category\Facades;

use Category\Queries\CategoryQuery;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Category\Category;
use Nette\SmartObject;

class CategoryFacade
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $categoryRepository;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }


    public function getCategory(CategoryQuery $query): ?Category
    {
        return $this->categoryRepository->fetchOne($query);
    }


    public function findCategories(CategoryQuery $query): ResultSet
    {
        return $this->categoryRepository->fetch($query);
    }

}