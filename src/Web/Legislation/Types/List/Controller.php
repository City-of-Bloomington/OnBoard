<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\List;

use Application\Models\Legislation\TypesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $types = [];
        $table = new TypesTable();

        foreach ($table->find() as $t) { $types[] = $t; }

        return new View($types);
    }
}
