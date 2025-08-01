<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\List;

use Application\Models\NotificationTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $notifications = [];

        $t = new NotificationTable();
        $l = $t->find();
        foreach ($l as $n) { $notifications[] = $n; }

        return new View($notifications);
    }
}
