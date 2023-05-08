<?php

namespace App\Sulu\DTO;

use DateTime;

class PageList {
    private $selection;

    public function __construct($pages)
    {
        $this->selection = $pages;
    }

    public function getPages($page = 0, $ppp = 10000000): array
    {
        $selection = $this->selection;

        return array_slice($selection, $page * $ppp, $ppp);;
    }

    public function getPagination($page = 0, $ppp = 10000000): array
    {
        $selection = $this->selection;
        $lastPage = intval(ceil( count($this->selection) / $ppp) -1);
        return [
            "totalItems" => count($selection),
            "firstPage" => 0,
            "lastPage" => $lastPage,
            "nextPage" => ($page +1 > $lastPage)? $lastPage : $page +1,
            "prevPage" => ($page - 1 < 0)? 0 : $page - 1,
            "currentPage" => $page,
        ];
    }

    public function filterByTemplates($templates = ['article']): PageList
    {
        $selection = $this->selection;
        $selection = array_filter($selection, function ($v, $k) use(&$templates) {
            return in_array($v['template'], $templates);
        }, ARRAY_FILTER_USE_BOTH);

        return new PageList($selection);
    }

    public function filterAuthored(): PageList
    {
        $now = new DateTime();

        $selection = $this->selection;
        $selection = array_filter($selection, function ($v, $k) use(&$now) {
            return $v['authored'] < $now;
        }, ARRAY_FILTER_USE_BOTH);

        return new PageList($selection);
    }


    public function sort(): PageList
    {
        $selection = $this->selection;

        usort($selection, function ($a, $b) {
            $al = $a['authored']->getTimestamp();
            $bl = $b['authored']->getTimestamp();
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? -1 : +1;
        });

        return new PageList($selection);
    }

}
