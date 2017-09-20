<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Category\Components\ICategoryTopicsOverviewControlFactory;
use Common\PublicModule\Presenters\PublicPresenter;
use Category\Facades\CategoryFacade;
use Category\Queries\CategoryQuery;
use Category\Category;

final class CategoryPresenter extends PublicPresenter
{

    /**
     * @var ICategoryTopicsOverviewControlFactory
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
            $this->setMetaTitle(sprintf('Forum %s is not public', $this->category->getTitle()), false);
            $this->setView('categoryIsNotPublic');
            return;
        }

        $this->setMetaTitle($this->category->getTitle());
    }


    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->category = $this->category;
    }


    protected function createComponentUnpinnedTopicsOverview()
    {
        $comp = $this->topicsOverviewControlFactory->create($this->category);

        return $comp;
    }


    protected function createComponentPinnedTopicsOverview()
    {
        $comp = $this->topicsOverviewControlFactory->create($this->category);

        $comp->setTitle('Pinned topics');
        $comp->onlyPinned();
        $comp->hideOnNoTopics();

        return $comp;
    }

}