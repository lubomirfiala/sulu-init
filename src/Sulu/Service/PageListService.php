<?php


namespace App\Sulu\Service;

use DateTime;
use Sulu\Bundle\WebsiteBundle\Navigation\NavigationMapper;
use Sulu\Bundle\WebsiteBundle\Resolver\StructureResolverInterface;
use Sulu\Component\Content\Mapper\ContentMapperInterface;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzer;
use Symfony\Component\HttpFoundation\RequestStack;

class PageListService
{
    private ContentMapperInterface $contentMapper;

    private StructureResolverInterface $structureResolver;

    private NavigationMapper $navigationMapper;

    private ?RequestAnalyzer $requestAnalyzer;

    private $locale = 'en';


    public function __construct(
        ContentMapperInterface $contentMapper,
        StructureResolverInterface $structureResolver,
        NavigationMapper $navigationMapper,
        RequestAnalyzer $requestAnalyzer = null,
        RequestStack $request
    )
    {
        $this->contentMapper = $contentMapper;
        $this->structureResolver = $structureResolver;
        $this->navigationMapper = $navigationMapper;
        $this->requestAnalyzer = $requestAnalyzer;
        $this->locale = $request->getCurrentRequest()->getLocale();
    }

    public function loadPages($uuid = null)
    {
        if ($uuid != null) {
            $articles = $this->getNavigation($uuid);
        } else {
            $articles = $this->getRootNavigation();
        }

        return $articles;
    }

    private function getRootNavigation() {

        $segment = $this->requestAnalyzer->getSegment();
        $webspaceKey = $this->requestAnalyzer->getWebspace()->getKey();
        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();

        return $this->navigationMapper->getRootNavigation(
            $webspaceKey,
            $locale,
            1000,
            true,
            null,
            false,
            $segment ? $segment->getKey() : null
        );

    }

    private function getNavigation($uuid) {

        $segment = $this->requestAnalyzer->getSegment();
        $webspaceKey = $this->requestAnalyzer->getWebspace()->getKey();
        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();

        return $this->navigationMapper->getNavigation(
            $uuid,
            $webspaceKey,
            $locale,
            1000,
            true,
            null,
            false,
            $segment ? $segment->getKey() : null
        );

    }

}
