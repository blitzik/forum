<?php declare(strict_types = 1);

namespace Category\Components;

use Category\Facades\CategoryFacade;
use Category\Queries\CategoryQuery;
use Common\Components\BaseControl;
use Doctrine\ORM\AbstractQuery;
use Nette\Utils\ArrayHash;

class CategoriesOverviewControl extends BaseControl
{
    /** @var CategoryFacade */
    private $categoryFacade;


    public function __construct(
        CategoryFacade $panelFacade
    ) {
        $this->categoryFacade = $panelFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();

        $q = new CategoryQuery();
        $q->withSection()
          ->withLastPost(['createdAt', 'topic'])
          ->withLastPostAuthor(['name'])
          ->withLastPostTopic(['id']);

        if (!$this->user->isLoggedIn()) {
            $q->onlyPublic();
        }
        $q->orderByPosition();

        $categoriesResultSet = $this->categoryFacade->findCategories($q);
        $categories = $categoriesResultSet->toArray(AbstractQuery::HYDRATE_ARRAY);

        if (empty($categories)) {
            $template->setFile(__DIR__ . '/noSectionsFound.latte');
            $template->render();
            return;
        }

        $sections = [];
        foreach ($categories as $category) {
            if (!isset($sections[$category['section']['position']])) {
                $sections[$category['section']['position']] = $category['section'];
            }
            $sections[$category['section']['position']]['categories'][$category['id']] = $category;
            $sections[$category['section']['position']]['lastPost'] = $category['lastPost'];
        }

        ksort($sections);
        $template->sections = ArrayHash::from($sections);
        unset($sections);

        $template->setFile(__DIR__ . '/overview.latte');
        $template->render();
    }
}


interface ICategoriesOverviewControlFactory
{
    /**
     * @return CategoriesOverviewControl
     */
    public function create(): CategoriesOverviewControl;
}