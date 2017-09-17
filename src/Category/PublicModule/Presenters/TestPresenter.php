<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Kdyby\Doctrine\EntityManager;

final class TestPresenter extends PublicPresenter
{
    /**
     * @var EntityManager
     * @inject
     */
    public $em;


    public function actionDefault()
    {

    }


    public function renderDefault()
    {

    }


    public function actionTest()
    {

    }


    public function renderTest()
    {

    }
}