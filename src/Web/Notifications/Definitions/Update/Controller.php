<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\Update;

use Application\Models\Notifications\Definition;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['definition_id'])) {
            try { $d = new Definition($_REQUEST['definition_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($d)) { return new \Web\Views\NotFoundView(); }

        $r = \Web\View::generateUrl('notifications.definitions.info', ['definition_id'=>$d->getId()]);

        if (isset($_POST['body'])) {
            $d->handleUpdate($_POST);

            try {
                $d->save();
                header("Location: $r");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($d, $r);
    }
}
