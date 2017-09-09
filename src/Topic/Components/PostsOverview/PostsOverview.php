<?php declare(strict_types = 1);

namespace Topic\Components;

use Common\Components\BaseControl;
use Doctrine\ORM\AbstractQuery;
use Post\Facades\PostFacade;
use Post\Queries\PostQuery;
use Nette\Utils\ArrayHash;
use Topic\Topic;

class PostsOverviewControl extends BaseControl
{
    /** @var PostFacade */
    private $postFacade;


    /** @var Topic */
    private $topic;


    public function __construct(
        Topic $topic,
        PostFacade $postFacade
    ) {
        $this->topic = $topic;
        $this->postFacade = $postFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/postsOverview.latte');


        $resultSet = $this->postFacade
                          ->findPosts(
                              (new PostQuery())
                              ->withAuthor(['name', 'registered', 'numberOfPosts'])
                              ->withAuthorRole(['name'])
                              ->byTopic($this->topic)
                              ->orderedByCreationTime()
                          );

        $posts = $resultSet->toArray(AbstractQuery::HYDRATE_ARRAY);

        $template->posts = ArrayHash::from($posts);
        $template->topic = $this->topic;


        $template->render();
    }
}


interface IPostsOverviewControlFactory
{
    /**
     * @param Topic $topic
     * @return PostsOverviewControl
     */
    public function create(Topic $topic): PostsOverviewControl;
}