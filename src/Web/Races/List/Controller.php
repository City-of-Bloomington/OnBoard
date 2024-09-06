<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\List;

use Application\Models\RaceTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $races = [];
        $table = new RaceTable();
        $res   = $table->find();
        foreach ($res as $r) { $races[] = $r; }

        return new View($races);
    }
}
