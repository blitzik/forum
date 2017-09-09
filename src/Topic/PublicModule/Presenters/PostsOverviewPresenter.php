<?php declare(strict_types = 1);

namespace Topic\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Topic\Components\IPostsOverviewControlFactory;
use Topic\Facades\TopicFacade;
use Topic\Queries\TopicQuery;
use Topic\Topic;

final class PostsOverviewPresenter extends PublicPresenter
{
    /**
     * @var IPostsOverviewControlFactory
     * @inject
     */
    public $postsOverviewControlFactory;

    /**
     * @var TopicFacade
     * @inject
     */
    public $topicFacade;


    /** @var Topic */
    private $topic;


    public function actionDefault($internalId)
    {
        $this->topic = $this->topicFacade
                            ->getTopic(
                                (new TopicQuery())
                                ->withCategory()
                                ->withCategorySection()
                                ->byId((int)$internalId)
                            );

        if (!$this->topic->isPublic() and !$this->user->isLoggedIn()) {
            $this->setMetaTitle(sprintf('Topic %s is not public', $this->topic->getTitle()), false);
            $this->setView('topicIsNotPublic');
            return;
        }

        $this->setMetaTitle($this->topic->getTitle());
    }


    protected function beforeRender()
	{
        parent::beforeRender();

        $this->template->topic = $this->topic;
    }


    protected function createComponentPostsOverview()
    {
        $comp = $this->postsOverviewControlFactory->create($this->topic);

        return $comp;
    }
}