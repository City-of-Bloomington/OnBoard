<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\List;

use Application\Models\Appointer;
use Application\Models\Committee;
use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $search = self::parseQueryParameters();
        $result = SeatTable::currentData($search);
        $data   = self::filter_viewable($result['results']);

        switch ($this->outputFormat) {
            case 'csv':
                return new \Web\Views\CSVView('Seats', $data);
            break;

            default:
                return new View($data, $search);
        }


    }

    /**
     * Creates valid request parameters
     */
    private static function parseQueryParameters(): array
    {
        $search = [];
        if (!empty($_GET['current'])) {
            try {
                $c = \DateTime::createFromFormat(DATE_FORMAT, $_GET['current']);
                $search['current'] = $c;
            }
            catch (\Exception $e) { }
        }
        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);
                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!empty($_GET['appointer_id'])) {
            try {
                $appointer = new Appointer($_GET['appointer_id']);
                $search['appointer_id'] = $appointer->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        return $search;
    }

    /**
     * Filter the data results to only the fields that are permitted
     */
    public static function filter_viewable($results): array
    {
        $data    = [];
        $fields  = ['email', 'phone', 'address', 'city', 'state', 'zip'];
        $canView = \Web\View::isAllowed('people', 'viewContactInfo');
        foreach ($results as $row)
        {
            if (!$canView) {
                foreach (['member', 'alternate'] as $p) {
                    foreach ($fields as $f) {
                        unset($row[$p.'_'.$f]);
                    }
                }
            }
            $data[] = $row;
        }
        return $data;
    }
}
