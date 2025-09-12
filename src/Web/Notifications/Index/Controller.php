<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Index;

use Application\Models\Notifications\DefinitionTable;
use Application\Models\Notifications\EmailQueue;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $emails = [];
        $search = self::prepareSearch();
        $result = null;

        $table  = new EmailQueue();
        $result = $table->find($search, 'created desc', true);
        $result->setCurrentPageNumber($page);
        $result->setItemCountPerPage(parent::ITEMS_PER_PAGE);
        foreach ($result as $e) { $emails[] = $e; }

        return new View(self::definitions(),
                        $emails,
                        $search,
                        $result ? $result->getTotalItemCount() : 0,
                        parent::ITEMS_PER_PAGE,
                        $page);
        return new View(self::definitions());
    }

    private static function definitions(): array
    {
        $defs = [];
        $t    = new DefinitionTable();
        $l    = $t->find();
        foreach ($l as $d) { $defs[] = $d; }
        return $defs;
    }

    private static function prepareSearch(): array
    {
        $s = [];
        $fields = ['event', 'committee_id'];
        foreach ($fields as $f) {
            if (!empty($_GET[$f])) { $s[$f] = $_GET[$f]; }
        }
        return $s;
    }
}
