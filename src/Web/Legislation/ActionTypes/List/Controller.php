<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\ActionTypes\List;

use Application\Models\Legislation\ActionTypesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $table = new ActionTypesTable();
        $types = $table->find();

        return new View($types);
    }
}
