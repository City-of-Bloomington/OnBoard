<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Notifications\Add;

use Application\Models\Notifications\Subscription;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url   = $_REQUEST['return_url'  ] ?? \Web\View::generateUri('profile.index');
        $event        = $_REQUEST['event'       ] ?? null;
        $committee_id = $_REQUEST['committee_id'] ?? null;

        if ($_SESSION['USER'] && $event && $committee_id) {
            try {
                $s = new Subscription();
                $s->handleUpdate([
                    'person_id'    => $_SESSION['USER']->getId(),
                    'committee_id' => $committee_id,
                    'event'        => $event
                ]);
                $s->save();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        header("Location: $return_url");
        exit();
    }
}
