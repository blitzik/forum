<?php declare(strict_types = 1);

namespace Setting\Facades;

use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;
use Setting\Services\SettingsLoader;

class SettingFacade
{
    use SmartObject;


    /** @var SettingsLoader */
    private $settingsLoader;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        SettingsLoader $settingsLoader
    ) {
        $this->em = $entityManager;
        $this->settingsLoader = $settingsLoader;
    }


    public function getAllSettings(): array
    {
        return $this->settingsLoader->getAllSettings();
    }
}