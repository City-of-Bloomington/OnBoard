<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\List;

use Application\Models\Liaison;
use Application\Models\LiaisonTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $type = (!empty($_GET['type']) && in_array($_GET['type'], Liaison::$types))
                ? $_GET['type']
                : Liaison::$types[0];

        $data = [];
        $res  = LiaisonTable::data(['type'=>$type, 'current'=>true]);
        foreach ($res['results'] as $row) { $data[] = $row; }

        switch ($this->outputFormat) {
            case 'email':
                return new MailMerge($data);
            break;

            case 'csv':
                return new \Web\Views\CSVView('Liaisons', $data);
            break;

            default:
                return new View($data);
        }
    }
}
