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
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['committee_id'])) {
            try {
                $committee = new Committee($params['committee_id']);
                $table     = new LegislationTable();
                $years     = $table->years(['committee_id'=>$committee->getId()]);
                return new View($years, $committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
