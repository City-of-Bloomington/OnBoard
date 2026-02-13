<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\List;

use Application\Models\ApplicationTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $apps   = [];
        $list   = null;
        $search = self::prepareSearch();

        $table = new ApplicationTable();
        $list  = $table->search(fields:$search, itemsPerPage:parent::ITEMS_PER_PAGE, currentPage:$page);

        return new View($list['rows'],
                        $search,
                        $list['total'],
                        parent::ITEMS_PER_PAGE,
                        $page);
    }

    private static function prepareSearch(): array
    {
        $search = ['current'=>time()];

        if (!empty($_GET['firstname'])) { $search['firstname'] = $_GET['firstname']; }
        if (!empty($_GET['lastname' ])) { $search['lastname' ] = $_GET['lastname' ]; }
        if (!empty($_GET['email'    ])) { $search['email'    ] = $_GET['email'    ]; }
        if (!empty($_GET['committee_id'])) { $search['committee_id'] = $_GET['committee_id']; }
        if (!empty($_GET['status'])) {
            if ($_GET['status'] == 'archived') {
                unset($search['current']);
                $search['archived'] = time();
            }
        }
        return $search;
    }
}
