<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Doctrine\ORM\OptimisticLockException;
use Kdyby\Doctrine\EntityManager;
use Category\Category;
use Account\Account;
use Topic\Topic;

final class TestPresenter extends PublicPresenter
{
    /**
     * @var EntityManager
     * @inject
     */
    public $em;


    public function actionDefault()
    {
        for ($i = 0; $i < 10; $i++) {
            if ($i == 0 or $i % 100 === null) {
                $this->em->clear();
                $category = $this->em->find(Category::class, 1);
                $author = $this->em->find(Account::class, 1);
            }

            $t1 = new Topic('abc', $author, $category);
            $this->em->persist($t1);

            $this->em->flush();
        }

        $this->terminate();
    }


    private function p()
    {
        try {
            $category = $this->em->find(Category::class, 1);
            $author = $this->em->find(Account::class, 1);

            $t1 = new Topic('abc', $author, $category);
            $this->em->persist($t1);

            $this->em->flush();

        } catch (OptimisticLockException $e) {
            $this->p();
        }
    }


    public function renderDefault()
    {

    }
}