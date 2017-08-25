<?php declare(strict_types = 1);

namespace Setting\Services;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Nette\Caching\IStorage;
use Nette\Caching\Cache;
use Nette\SmartObject;
use Nette\Utils\Arrays;
use Setting\Setting;

class SettingsLoader
{
    use SmartObject;

    /** @var Cache */
    private $cache;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager, IStorage $storage)
    {
        $this->em = $entityManager;
        $this->cache = new Cache($storage, 'forum.settings');
    }


    public function getAllSettings(): array
    {
        return $this->cache->load('forum.settings', function () {
            $settings = $this->em->createQuery(
                'SELECT s.name, s.value FROM ' . Setting::class . ' s INDEX BY s.name'
            )->getArrayResult();

            return Arrays::associate($settings, 'name=value');
        });
    }
}