<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Statuses\List;

use Application\Models\Legislation\StatusesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $table = new StatusesTable();
        $list  = $table->find();

        return new View($list);
    }
}
