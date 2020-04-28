<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

class Paginator
{
    public $itemsPerPage      = 20;
    public $currentPageNumber = 1;
    public $totalItemCount    = 0;
    public $pageRange         = 10;

    public function __construct(int $totalItemCount, ?int $itemsPerPage=20, ?int $currentPageNumber=1, ?int $pageRange=10)
    {
        $this->totalItemCount    = $totalItemCount;
        $this->itemsPerPage      = $itemsPerPage;
        $this->currentPageNumber = $currentPageNumber < 1 ? 1 : $currentPageNumber;
        $this->pageRange         = $pageRange;
    }

    /**
     * @return \stdClass
     */
    public function getPages(): \stdClass
    {
        $pageRange  = $this->pageRange;
        $pageNumber = $this->currentPageNumber;
        $pageCount  = (int)ceil($this->totalItemCount / $this->itemsPerPage);

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = ceil($pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount;
        }
        else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $pageRange;
        }

        $pages = [];
        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        $p = new \stdClass();
        $p->first   = 1;
        $p->last    = $pageCount;
        $p->current = $this->currentPageNumber;
        if ($this->currentPageNumber > 1)          { $p->previous = $this->currentPageNumber - 1; }
        if ($this->currentPageNumber < $pageCount) { $p->next     = $this->currentPageNumber + 1; }
        $p->pagesInRange = $pages;
        return $p;
    }
}
