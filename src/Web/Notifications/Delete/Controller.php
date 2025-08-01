<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Delete;

use Application\Models\Notification;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['notification_id'])) {
            try { $n = new Notification($_REQUEST['notification_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($n)) { return new \Web\Views\NotFoundView(); }

        $r = \Web\View::generateUri('notifications.index');

        try { $n->delete(); }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        header("Location: $r");
        exit();
    }
}
