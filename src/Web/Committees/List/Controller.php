<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\List;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $search = ['current' => true];
        if (isset($_GET['current']) && !$_GET['current']) {
            $search['current'] = false;
        }
        $data = Committee::data($search);

        switch ($this->outputFormat) {
            case 'csv':
                return new \Web\Views\CSVView('Committees', $data);
            break;

            case 'json':
                return new \Web\Views\JSONView($data);
            break;

            default:
                return new View($data, $search['current']);
        }
    }
}
