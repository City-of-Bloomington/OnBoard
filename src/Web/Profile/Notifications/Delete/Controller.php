<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Notifications\Delete;

use Application\Models\Notifications\Subscription;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['subscription_id'])) {
            try {
                $s = new Subscription($_REQUEST['subscription_id']);
                if ($s->getPerson_id() == $_SESSION['USER']->getId()) {
                    $s->delete();
                }
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

            $return_url = $_REQUEST['return_url'] ?? \Web\View::generateUri('profile.index');
            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
