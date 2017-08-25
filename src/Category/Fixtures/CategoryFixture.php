<?php declare(strict_types = 1);

namespace Category\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use blitzik\Authorization\Resource;
use blitzik\Routing\Url;
use Category\Category;
use Category\Section;

class CategoryFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);

        $this->loadTestingPanels($manager);
        $this->loadTestingCategories($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('Category:Public:Homepage', $manager);
        $ug->addUrl('', 'default');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager); // todo
    }


    private function loadTestingPanels(ObjectManager $manager)
    {
        $section1 = new Section('Section 1');
        $manager->persist($section1);

        $section2 = new Section('Section 2');
        $section2->changePosition(1);
        $manager->persist($section2);

        $section3 = new Section('Section 3');
        $section3->changePosition(2);
        $section3->setAsPrivate();
        $manager->persist($section3);

        $this->addReference('section1', $section1);
        $this->addReference('section2', $section2);
        $this->addReference('section3', $section3);
    }


    private function loadTestingCategories(ObjectManager $manager)
    {
        $description = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit';

        $urlGenerator = new UrlGenerator('Category:Public:Category', $manager);

        $categories = [];
        $categories[] = $this->addCategory('Category One', $description, 0, 'section1', $urlGenerator, $manager);
        $categories[] = $this->addCategory('Category Two', $description, 1, 'section1', $urlGenerator, $manager);

        $categories[] = $this->addCategory('Category Three', $description, 0, 'section2', $urlGenerator, $manager);
        $categories[] = $this->addCategory('Category Four', $description, 1, 'section2', $urlGenerator, $manager);

        $categories[] = $this->addCategory('Category Five', $description, 0, 'section3', $urlGenerator, $manager);
        $categories[] = $this->addCategory('Category Six', $description, 1, 'section3', $urlGenerator, $manager);

        $manager->flush();

        /** @var Category $category */
        foreach ($categories as $category) {
            $categoryUrl = UrlGenerator::create(sprintf('c%s-%s', $category->getId(), $category->getTitle()), true, 'Category:Public:Category', 'default', (string)$category->getId());
            $manager->persist($category);

            $shortUrl = UrlGenerator::create(sprintf('c%s', $category->getId()), true, 'Category:Public:Category', 'default', (string)$category->getId());
            $shortUrl->setRedirectTo($categoryUrl);
            $manager->persist($shortUrl);
        }
    }


    private function addCategory(string $title, string $description, int $position, string $panelReferenceName, UrlGenerator $urlGenerator, ObjectManager $manager): Category
    {
        $category = new Category($title, $this->getReference($panelReferenceName));
        $category->setDescription($description);
        $category->changePosition($position);
        $manager->persist($category);
        $this->addReference(sprintf('catg_%s', str_replace(' ', '_', strtolower($title))), $category);

        return $category;
    }

}