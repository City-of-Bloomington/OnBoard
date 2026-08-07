<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\List;

use Application\Models\Liaison;
use Application\Models\LiaisonTable;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'csv', 'json', 'email'];

    public function __invoke(array $params): \Web\View
    {
        $s    = self::prepareSearch();
        $t    = new LiaisonTable();
        $r    = $t->data($s);
        $data = self::liaison_data($r);

        switch ($this->outputFormat) {
            case 'email':
                return new MailMerge($data);

            case 'csv':
                return new \Web\Views\CSVView('Liaisons', $data);

            case 'json':
                return new \Web\Views\JSONView($data);

            default:
                return new View($data, $s);
        }
    }

    private static function prepareSearch(): array
    {
        $s = ['current'=>true];
        if (!empty($_GET['type'  ]) && in_array($_GET['type'  ], LiaisonTable::$valid_types   )) { $s['type'  ] = $_GET['type'  ]; }
        if (!empty($_GET['status']) && in_array($_GET['status'], LiaisonTable::$valid_statuses)) { $s['status'] = $_GET['status']; }
        if (!empty($_GET['committee_id'])) { $s['committee_id'] = (int)$_GET['committee_id']; }
        return $s;
    }

    /**
     * Filters liaison data to only the fields that are permitted
     */
    private static function liaison_data($results): array
    {
        $canView = \Web\View::isAllowed('people', 'viewContactInfo');

        $data = [];
        foreach ($results as $row) {
            if (!$canView) {
                unset($row['email']);
                unset($row['phone']);
            }
            $data[] = $row;
        }
        return $data;
    }
}
