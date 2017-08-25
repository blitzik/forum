<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Category\Components\ICategoriesOverviewControlFactory;
use Common\PublicModule\Presenters\PublicPresenter;

final class HomepagePresenter extends PublicPresenter
{
    /**
     * @var ICategoriesOverviewControlFactory
     * @inject
     */
    public $categoriesOverviewControlFactory;


    public function actionDefault()
    {
        $this['metaTitle']->setTitle($this->globalSettings['forumTitle']);
    }


    public function renderDefault()
    {
    }


    protected function createComponentCategoriesOverview()
    {
        $comp = $this->categoriesOverviewControlFactory->create();

        return $comp;
    }
}