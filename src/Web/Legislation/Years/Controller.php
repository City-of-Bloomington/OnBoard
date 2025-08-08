<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Years;

use Application\Models\Committee;
use Application\Models\Legislation\LegislationTable;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'csv', 'json'];

    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['committee_id'])) {
            try {
                $committee = new Committee($params['committee_id']);
                $search    = ['committee_id'=>$committee->getId()];
                if (!empty($_GET['type_id'])) { $search['type_id'] = (int)$_GET['type_id']; }

                $table     = new LegislationTable();
                $years     = $table->years($search);
                switch ($this->outputFormat) {
                    case 'csv':
                        return new \Web\Views\CSVView($years);
                    break;

                    case 'json':
                        return new \Web\Views\JSONView($years);
                    break;

                    default:
                        return new View($years, $search, $committee);
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
