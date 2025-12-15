<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Models\Committee;
use Application\Models\Reports\ReportsTable;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'json'];

    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        $table = new ReportsTable();
        $search = isset($committee) ? ['committee_id'=>$committee->getId()] : [];

        switch ($this->outputFormat) {
            case 'json':
                $data = [];
                $list = $table->find($search, 'reportDate desc');
                foreach ($list['rows'] as $r) { $data[] = $r->toArray(); }
                return new \Web\Views\JSONView($data);
            break;

            default:
                $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

                $sort = 'desc';
                if ( !empty($_GET['sort'])) {
                    $sort = $_GET['sort']=='asc' ? 'asc' : 'desc';
                }


                $list  = $table->find($search, "reportDate $sort", parent::ITEMS_PER_PAGE, $page);
                return new View($list['rows'],
                                $search,
                                $sort,
                                $list['total'],
                                parent::ITEMS_PER_PAGE,
                                $page,
                                $committee ?? null);
        }
    }
}
