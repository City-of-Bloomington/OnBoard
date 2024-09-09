<?php
/**
 * @copyright 2014-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Appointers\List;

use Application\Models\AppointerTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $table = new AppointerTable();
        $appointers = $table->find();

        return new View($appointers);
    }
}