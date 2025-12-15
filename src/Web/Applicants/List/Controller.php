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
        $res   = $table->search($search, ['lastname','firstname'], parent::ITEMS_PER_PAGE, $page);

        return new View($res['rows'],
                        $search,
                        $res['total'],
                        parent::ITEMS_PER_PAGE,
                        $page);
    }

    private static function prepareSearch(): array
    {
        $search = [];
        foreach (ApplicantTable::$searchable_fields as $f) {
            if (!empty($_GET[$f])) { $search[$f] = $_GET[$f]; }
        }
        return $search;
    }
}
