<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Departments\List;

use Application\Models\DepartmentTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $t = new DepartmentTable();
        $l = $t->find();
        return new View($l['rows']);
    }
}
