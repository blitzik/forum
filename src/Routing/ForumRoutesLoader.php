<?php declare(strict_types=1);

namespace blitzik\Routing\RoutesLoader;

use blitzik\Router\RoutesLoader\IRoutesLoader;
use Nette\Caching\Storages\FileStorage;
use blitzik\Routing\Queries\UrlQuery;
use Nette\Caching\Storages\IJournal;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\FileSystem;
use Kdyby\Monolog\Logger;
use Nette\Caching\Cache;
use blitzik\Routing\Url;
use Nette\SmartObject;

final class ForumRoutesLoader implements IRoutesLoader
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $urlRepository;

    /** @var Logger */
    private $logger;


    /** @var EntityManager */
    private $em;


    /** @var string */
    private $tempDir;

    /** @var IJournal */
    private $journal;

    /** @var Cache[] */
    private $caches = [];


    /** @var int */
    private $filesPerNamespace = 1000;


    public function __construct(
        string $tempDir,
        EntityManager $entityManager,
        IJournal $journal,
        Logger $logger
    ) {
        $this->tempDir = $tempDir;
        $this->em = $entityManager;
        $this->logger = $logger->channel('databaseRouting');
        $this->journal = $journal;

        $this->urlRepository = $this->em->getRepository(Url::class);

        if (!file_exists($tempDir)) {
            FileSystem::createDir($tempDir);
        }

        $this->caches['routes'] = new Cache(new FileStorage($tempDir, $journal), 'routes');
    }


    public function loadUrlByPath(string $urlPath): ?\blitzik\Router\Url
    {
        $cache = $this->caches['routes'];

        $firstDashPosition = mb_strpos($urlPath, '-');
        if ($firstDashPosition === false) {
            if (is_numeric($urlPath)) { // short url of Topic
                $cache = $this->getCache($urlPath, 'topic_in');
            }
        } else {
            $i = mb_substr($urlPath, 0, $firstDashPosition);
            if (is_numeric($i)) { // full url of Topic
                $cache = $this->getCache($i, 'topic_in');
            }
        }

        /** @var Url $urlEntity */
        $urlEntity = $cache->load($urlPath, function (& $dependencies) use ($urlPath) {
            /** @var Url $urlEntity */
            $urlEntity = $this->urlRepository->fetchOne(
                (new UrlQuery())
                ->withRedirectionUrl()
                ->byPath($urlPath)
            );

            if ($urlEntity === null) {
                $this->logger->addError(sprintf('Page not found. URL_PATH: %s', $urlPath));
                return null;
            }

            $dependencies = [Cache::TAGS => $urlEntity->getCacheKey()];
            return $urlEntity;
        });

        if ($urlEntity === null) {
            return null;
        }

        return $urlEntity->convertToRouterUrl();
    }


    public function loadUrlByDestination(string $presenter, string $action, string $internalId = null): ?\blitzik\Router\Url
    {
        $cache = $this->caches['routes'];

        $urlPathCacheKey = sprintf('%s:%s:%s', $presenter, $action, $internalId);
        if ($presenter === 'Topic:Public:PostsOverview' and $action === 'default') {
            $cache = $this->getCache($internalId, 'topic_out');
        }

        /** @var Url $urlEntity */
        $urlEntity = $cache->load($urlPathCacheKey, function (& $dependencies) use ($presenter, $action, $internalId) {
            $urlEntity = $this->getUrlEntity($presenter, $action, $internalId);
            if ($urlEntity === null) {
                $this->logger
                    ->addWarning(
                        sprintf(
                            'No route found | presenter: %s | action: %s | id %s',
                            $presenter,
                            $action,
                            $internalId
                        )
                    );
                return null;
            }

            $dependencies = [Cache::TAGS => $urlEntity->getCacheKey()];
            return $urlEntity;
        });

        if ($urlEntity === null) {
            return null;
        }

        return $urlEntity->convertToRouterUrl();
    }


    private function getUrlEntity(string $presenter, string $action, string $internalId = null): ?Url
    {
        $q = new UrlQuery();
        $q->byPresenter($presenter);
        $q->byAction($action);

        $qb = $this->em->createQueryBuilder();
        $qb->select('u, rt')
            ->from(Url::class, 'u')
            ->leftJoin('u.urlToRedirect', 'rt')
            ->where('u.presenter = :p AND u.action = :a')
            ->setParameters(['p' => $presenter, 'a' => $action]);

        if ($internalId !== null) {
            $qb->andWhere('u.internalId = :i')
                ->setParameter('i', $internalId);
        }

        /** @var Url[] $urls */
        $urls = $qb->getQuery()->setMaxResults(1)->getResult();

        if (empty($urls)) {
            return null;
        }

        return $urls[0];
    }


    private function getCache(string $topicId, string $nsBase): Cache
    {
        $nsNumber = (int)floor($topicId / $this->filesPerNamespace);
        $ns = sprintf('%s_%s', $nsNumber, $nsBase);
        if (!array_key_exists($ns, $this->caches)) {
            $this->caches[$ns] = new Cache(new FileStorage($this->tempDir, $this->journal), $ns);
        }

        return $this->caches[$ns];
    }

}