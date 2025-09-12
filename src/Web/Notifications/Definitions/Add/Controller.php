<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\Add;

use Application\Models\Notifications\Definition;
use Web\Notifications\Definitions\Update\View as UpdateView;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $n = new Definition();
        $r = \Web\View::generateUri('notifications.index');

        if (isset($_POST['body'])) {
            $n->handleUpdate($_POST);
            try {
                $n->save();
                header("Location: $r");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new UpdateView($n, $r);
    }
}
