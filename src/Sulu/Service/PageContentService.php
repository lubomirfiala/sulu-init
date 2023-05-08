<?php

namespace App\Sulu\Service;

use Sulu\Bundle\WebsiteBundle\Resolver\StructureResolverInterface;
use Sulu\Component\Content\Mapper\ContentMapperInterface;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzer;
use Symfony\Component\HttpFoundation\RequestStack;

class PageContentService
{
    private string $locale = 'cs';

    private array $defaultContentTargets = [
        'content',
        'contentLeft',
        'contentRight',
        'content1',
        'content2',
    ];

    public function __construct (
        private ContentMapperInterface $contentMapper,
        private RequestAnalyzer $requestAnalyzer,
        private StructureResolverInterface $structureResolver,
        RequestStack $request
    ) {
        $this->locale = $request->getCurrentRequest()->getLocale();
    }

    public function resolveMultiple($uuids, $attributes) {
        $properties = [];
        $pages = [];
        foreach ($attributes as $attribute) {
            $properties[$attribute] = $attribute;
        }

        foreach ($uuids as $key => $uuid) {
            $contentStructure = $this->contentMapper->load(
                $uuid,
                $this->requestAnalyzer->getWebspace()->getKey(),
                $this->locale
            );
            $resolvedStructure = $this->structureResolver->resolve(
                $contentStructure,
                false,
                \array_values($properties)
            );

            $pages[$key] = $resolvedStructure;

        }

        return $pages;
    }

    public function resolveSingle($uuid, $attributes) {
        $properties = [];
        foreach ($attributes as $attribute) {
            $properties[$attribute] = $attribute;
        }

        $contentStructure = $this->contentMapper->load(
            $uuid,
            $this->requestAnalyzer->getWebspace()->getKey(),
            $this->locale
        );
        $resolvedStructure = $this->structureResolver->resolve(
            $contentStructure,
            false,
            \array_values($properties)
        );

        return $resolvedStructure;

    }

    public function forEachBlockOfType($pageContent, $type, $callback, $targets = []) {
        $targets = (count($targets)) ? $targets : $this->defaultContentTargets;
        foreach($pageContent as $key => $block) {
            if ($block['type'] == $type) {
                $block = $callback($block);
                $pageContent[$key] = $block;
            }
            foreach ($targets as $target) {
                if (isset($block[$target])) {
                    $pageContent[$key][$target] = $this->forEachBlockOfType($block[$target], $type, $callback, $targets);
                }
            }
        }
        return $pageContent;
    }

    //TODO add separate values function
}
