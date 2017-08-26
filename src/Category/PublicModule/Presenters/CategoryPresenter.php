<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Category\Components\ITopicsOverviewControlFactory;
use Common\PublicModule\Presenters\PublicPresenter;
use Category\Facades\CategoryFacade;
use Category\Queries\CategoryQuery;
use Category\Category;

final class CategoryPresenter extends PublicPresenter
{
    /**
     * @var ITopicsOverviewControlFactory
     * @inject
     */
    public $topicsOverviewControlFactory;


    /**
     * @var CategoryFacade
     * @inject
     */
    public $categoryFacade;


    /** @var Category|null */
    private $category;


    public function actionDefault($internalId)
    {
        $this->category = $this->categoryFacade
                               ->getCategory(
                                   (new CategoryQuery())
                                   ->withSection()
                                   ->byId((int)$internalId)
                               );

        if (!$this->category->isPublic() and !$this->user->isLoggedIn()) {
            $this->setView('categoryIsNotPublic');

        } else {
            $this->setView('topicsOverview');
        }
    }


    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->category = $this->category;
    }


    protected function createComponentTopicsOverview()
    {
        $comp = $this->topicsOverviewControlFactory->create($this->category);

        return $comp;
    }


}