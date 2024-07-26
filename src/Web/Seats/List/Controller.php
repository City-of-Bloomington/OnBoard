<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\List;

use Application\Models\SeatTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $data   = [];
        $params = self::parseQueryParameters();
        $result = SeatTable::currentData($params);
        foreach ($result['results'] as $row) {
            $data[] = $row;
        }

        switch ($this->outputFormat) {
            case 'csv':
                $filename = APPLICATION_NAME.'-seats-'.date('Ymd');
                return new \Web\Views\CSVView($filename, $data);
            break;

            default:
                return new View($data);
        }


    }

    /**
     * Creates a valid date from the request parameters
     *
     * In the future, we can expand this to accomodate safe parsing for
     * more parameters.
     */
    private static function parseQueryParameters(): array
    {
        if (!empty($_GET['current'])) {
            try {
                $c = \DateTime::createFromFormat(DATE_FORMAT, $_GET['current']);
                return ['current'=>$c];
            }
            catch (\Exception $e) { }
        }
        return [];
    }

}
