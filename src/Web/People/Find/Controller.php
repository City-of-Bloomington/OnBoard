<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Find;

use Application\Models\PeopleTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $people = null;


        if (isset($_GET['firstname'])) {
            $table = new PeopleTable();
            $people = $table->search($_GET, 'lastname', true);

            $people->setCurrentPageNumber($page);
            $people->setItemCountPerPage(parent::ITEMS_PER_PAGE);
        }


        return new View($people,
                        $people ? $people->getTotalItemCount() : 0,
                        parent::ITEMS_PER_PAGE,
                        $page);
    }
}
