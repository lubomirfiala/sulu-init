<?php


namespace App\Sulu\Controller\Website;

use App\Sulu\DTO\PageList;
use App\Sulu\Service\PageContentService;
use App\Sulu\Service\PageListService;
use Sulu\Bundle\WebsiteBundle\Controller\DefaultController;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class PageController extends DefaultController
{
    public function __construct(
        private PageListService $pageListService,
        private PageContentService $pageContentService,
        private RequestStack $requestStack,
        private int $pageListBlockCounter = 0,
    ) {
    }


    protected function getAttributes($attributes, StructureInterface $structure = null, $preview = false)
    {
        $attributes = parent::getAttributes($attributes, $structure, $preview);

        $attributes['content']['content'] = $this
            ->pageContentService->forEachBlockOfType(
                $attributes['content']['content'],
                'page-list-block',
                function($block) {

                    // load all pages under parent and filter by template
                    $pages = $this->pageListService->loadPages($block['parent']);
                    $pageList = new PageList($pages);
                    $pageList = $pageList
                        ->filterByTemplates([$block['template']])
                        ->filterAuthored()
                        ->sort();

                    // get query data for pagination
                    $paginationKey = 'page-list-' . $this->pageListBlockCounter;
                    $pagination = [
                        'page' => intval($this->requestStack->getCurrentRequest()->get($paginationKey)),
                        'ppp' => 32,
                    ];

                    // load content of pages for list
                    $contentAttributes = [
                        'title',
                        'perex',
                        'image',
                        'cardSize',
                        'url',
                    ];
                    $pages = $pageList->getPages($pagination['page'], $pagination['ppp']);
                    $uuids = array_map(function($page) { return $page['uuid']; }, $pages );
                    $pages = $this->pageContentService->resolveMultiple($uuids, $contentAttributes);

                    // prepare data for block
                    $block['articles'] = $pages;
                    $block['pagination'] = $pageList->getPagination($pagination['page'], $pagination['ppp']);
                    $block['paginationTarget'] = $paginationKey;

                    $this->pageListBlockCounter++;

                    return $block;
                });

        return $attributes;
    }
}
