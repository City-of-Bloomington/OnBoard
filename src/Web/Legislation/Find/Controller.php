<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Find;

use Application\Models\Legislation\LegislationTable;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $_GET['parent_id'] = null;
        foreach (['type', 'status'] as $f) {
            if (!empty($_GET[$f]) && empty($_GET["{$f}_id"])) {
                try {
                    $class = '\\Application\\Models\Legislation\\'.ucfirst($f);
                    $$f    = new $class($_GET[$f]);
                    $_GET["{$f}_id"] = $$f->getId();
                }
                catch (\Exception $e) { }
                unset($_GET[$f]);
            }
        }

        $table = new LegislationTable();

        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }


        if ($this->template->outputFormat == 'html') {
            $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
            $list  = $table->find($_GET, 'year desc, number desc', true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);

            $legislation = [];
            foreach ($list as $l) { $legislation[] = $l; }


            return new View($legislation,
                            $_GET,
                            $list->getTotalItemCount(),
                            $page,
                            $list->getItemCountPerPage(),
                            $committee ?? null);

        }
        else {
            $legislation = [];
            foreach ($table->find($_GET) as $l) {
                $legislation[] = $l->toArray();
            }
            return new \Web\JSONView($legislation);
        }
    }
}