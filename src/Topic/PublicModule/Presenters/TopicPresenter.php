<?php declare(strict_types = 1);

namespace Topic\PublicModule\Presenters;

use Topic\Components\ITopicCreationFormControlFactory;
use Common\PublicModule\Presenters\PublicPresenter;
use Category\Facades\CategoryFacade;
use Category\Queries\CategoryQuery;
use Category\Category;
use Topic\Topic;

final class TopicPresenter extends PublicPresenter
{
    /**
     * @var ITopicCreationFormControlFactory
     * @inject
     */
    public $topicCreationFormFactory;

    /**
     * @var CategoryFacade
     * @inject
     */
    public $categoryFacade;


    /** @var Category */
    private $category;


    protected function startup()
    {
        parent::startup();

        $this->setMetaTitle('Topic creation');
        if (!$this->user->isLoggedIn()) {
            $this->setView('notLoggedIn');
            $this->_backLink = $this->storeRequest();
            $this->sendTemplate();
        }
    }


    public function actionNew($cid)
    {
        $this->category = $this->categoryFacade
                               ->getCategory(
                                   (new CategoryQuery())
                                   ->byId((int)$cid)
                               );

        if ($this->category === null) {
            $this->redirect(':Category:Public:Homepage:default');
        }

        $this->setMetaTitle(sprintf('%s - New topic', $this->category->getTitle()));
    }


    public function renderNew($cid)
    {
        $this->template->category = $this->category;
    }


    protected function createComponentTopicForm()
    {
        $comp = $this->topicCreationFormFactory
                     ->create($this->user->getIdentity(), $this->category);

        $comp->onSuccessfulCreation[] = function (Topic $topic) {
            $this->redirect(':Topic:Public:PostsOverview:default', ['internalId' => (string)$topic->getId()]);
        };

        return $comp;
    }

}