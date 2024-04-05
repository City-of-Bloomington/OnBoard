<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\List;

use Application\Models\TagsTable;


class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $table = new TagsTable();
        $tags = $table->find();
        return new View($tags);
    }
}
