<?php

namespace App\Sulu\Service;

use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Exception\ResourceLocatorNotFoundException;
use Sulu\Component\Content\Types\ResourceLocator\Strategy\ResourceLocatorStrategyPoolInterface;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
class PageLocator
{
    public function __construct(
        private DocumentManagerInterface $documentManager,
        private ResourceLocatorStrategyPoolInterface $resourceLocatorStrategyPool,
    )
    {

    }
    public function getDocument($path, $webspaceKey = 'example', $locale = 'cs') {

        $resourceLocatorStrategy = $this
            ->resourceLocatorStrategyPool
            ->getStrategyByWebspaceKey(
                $webspaceKey
            );

        try {
            // load content by url ignore ending trailing slash
            /** @var PageDocument $document */
            $document = $this->documentManager->find(
                $resourceLocatorStrategy->loadByResourceLocator(
                    \rtrim($path, '/'),
                    $webspaceKey,
                    $locale
                ),
                $locale,
                [
                    'load_ghost_content' => false,
                ]
            );
            return $document;
        } catch (ResourceLocatorNotFoundException $exc) {
            return null;
        }
    }
}
