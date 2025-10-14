<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Vacancies;

use Application\Models\SeatTable;
use Web\Seats\List\Controller as ListController;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'csv'];

    public function __invoke(array $params): \Web\View
    {
        $search = $this->parseQueryParameters();
        $search['vacant'] = true;
        $result = SeatTable::currentData($search);
        $data   = ListController::filter_viewable($result['results']);

        switch ($this->outputFormat) {
            case 'csv':
                return new \Web\Views\CSVView('Vacancies', $data);
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
