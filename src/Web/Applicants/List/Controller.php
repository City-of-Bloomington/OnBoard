<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\List;

use Application\Models\ApplicantTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $page       = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $list       = null;
        $search     = self::prepareSearch();
        $applicants = [];

        $table = new ApplicantTable();
        $list  = $table->search($search, ['lastname','firstname'], true);

        $list->setCurrentPageNumber($page);
        $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);
        foreach ($list as $p) { $applicants[] = $p; }

        return new View($applicants,
                        $search,
                        $list ? $list->getTotalItemCount() : 0,
                        parent::ITEMS_PER_PAGE,
                        $page);
    }

    private static function prepareSearch(): array
    {
        $search = [];
        if (!empty($_GET['firstname'])) { $search['firstname'] = $_GET['firstname']; }
        if (!empty($_GET['lastname' ])) { $search['lastname' ] = $_GET['lastname' ]; }
        if (!empty($_GET['email'    ])) { $search['email'    ] = $_GET['email'    ]; }

        return $search;
    }
}
