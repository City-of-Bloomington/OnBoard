<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        $search = self::prepareSearch();

        if (isset($_GET['firstname'])) {
            $table = new PeopleTable();
            $list  = $table->search($search, 'lastname', parent::ITEMS_PER_PAGE, $page);
        }

        return new View($list['rows'] ?? [],
                        $search,
                        $list['total'] ?? 0,
                        parent::ITEMS_PER_PAGE,
                        $page);
    }

    private static function prepareSearch(): array
    {
        $search = [];
        if (!empty($_GET['firstname'])) { $search['firstname'] = $_GET['firstname']; }
        if (!empty($_GET['lastname' ])) { $search['lastname' ] = $_GET['lastname' ]; }

        if (\Web\View::isAllowed('people', 'viewContactInfo')) {
            if (!empty($_GET['email'])) { $search['email'] = $_GET['email']; }
        }
        else {
            $search['involvement'] = true;
        }

        return $search;
    }
}
