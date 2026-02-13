<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\List;

use Application\Models\Legislation\TypesTable;

class Controller extends \Web\Controller
{
    protected $valid_output_formats = ['html', 'csv', 'json'];

    public function __invoke(array $params): \Web\View
    {
        $t = new TypesTable();
        $r = $t->find();

        switch ($this->outputFormat) {
            case 'csv':
                return new \Web\Views\CSVView('LegislationTypes', self::data($r['rows']));
            break;

            case 'json':
                return new \Web\Views\JSONView(self::data($r['rows']));
            break;

            default:
                return new View(iterator_to_array($r['rows']));

        }
    }

    private static function data($result): array
    {
        $data = [];
        foreach ($result as $t) { $data[] = $t->toArray(); }
        return $data;
    }
}
