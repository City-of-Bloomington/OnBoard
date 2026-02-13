<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\ActionTypes\List;

use Application\Models\Legislation\ActionTypesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $t = new ActionTypesTable();
        $r = $t->find();
        return new View($r['rows']);
    }
}
