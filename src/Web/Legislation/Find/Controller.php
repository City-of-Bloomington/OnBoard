<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Find;

use Application\Models\Legislation\LegislationTable;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'json'];

    public function __invoke(array $params): \Web\View
    {
        try { $committee = new Committee($params['committee_id']); }
        catch (\Exception $e) { return new \Web\Views\NotFoundView(); }

        $search = self::prepareSearch($committee);
        $table  = new LegislationTable();

        if ($this->outputFormat == 'html') {
            $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
            $list  = $table->search($search, 'year desc, number desc', parent::ITEMS_PER_PAGE, $page);

            return new View($list['rows'],
                            $search,
                            $list['total'],
                            $page,
                            parent::ITEMS_PER_PAGE,
                            $committee);

        }
        else {
            $list = $table->search($search);
            return new \Web\Views\JSONView($list['rows']);
        }
    }

    private static function prepareSearch(Committee $c): array
    {
        $s = [
            'committee_id' => $c->getId(),
               'parent_id' => null
        ];

        if (!empty($_GET['number'   ])) { $s['number'   ] = $_GET['number']; }
        if (!empty($_GET['year'     ])) { $s['year'     ] = (int)$_GET['year'     ]; }
        if (!empty($_GET['status_id'])) { $s['status_id'] = (int)$_GET['status_id']; }
        if (!empty($_GET[  'type_id'])) { $s[  'type_id'] = (int)$_GET[  'type_id']; }
        return $s;
    }
}
