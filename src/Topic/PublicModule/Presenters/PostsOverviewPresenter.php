<?php declare(strict_types = 1);

namespace Topic\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Topic\Components\IPostsOverviewControlFactory;
use Topic\Components\IPostFormControlFactory;
use Topic\Facades\TopicFacade;
use Topic\Queries\TopicQuery;
use Topic\Topic;
use Post\Post;

final class PostsOverviewPresenter extends PublicPresenter
{
    /**
     * @var IPostsOverviewControlFactory
     * @inject
     */
    public $postsOverviewControlFactory;

    /**
     * @var IPostFormControlFactory
     * @inject
     */
    public $postFormControlFactory;

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


    protected function createComponentPostForm()
    {
        $comp = $this->postFormControlFactory->create($this->user->getIdentity(), $this->topic);

        $comp->onSuccessfulCreation[] = function (Post $post) {
            $this->redirect(sprintf('this#post-%s', $post->getId()));
        };

        return $comp;
    }
}