<?php declare(strict_types = 1);

namespace Category\Facades;

use Kdyby\Doctrine\EntityManager;
use Category\Queries\SectionQuery;
use Kdyby\Doctrine\ResultSet;
use Nette\SmartObject;
use Category\Section;

class SectionFacade
{
    use SmartObject;


    private $sectionRepository;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->sectionRepository = $entityManager->getRepository(Section::class);
    }


    public function getSection(SectionQuery $query): ?Panel
    {
        return $this->sectionRepository->fetchOne($query);
    }


    public function findSections(SectionQuery $query): ResultSet
    {
        return $this->sectionRepository->fetch($query);
    }


}