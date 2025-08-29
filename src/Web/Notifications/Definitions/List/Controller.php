<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\List;

use Application\Models\Notifications\DefinitionTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $defs = [];

        $t = new DefinitionTable();
        $l = $t->find();
        foreach ($l as $d) { $defs[] = $d; }

        return new View($defs);
    }
}
