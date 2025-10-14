<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Notes\Add;

use Application\Models\Committee;
use Application\Models\Committees\Note;
use Web\Committees\Notes\Update\View as UpdateView;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($committee)) {
            $note = new Note();
            $note->setCommittee($committee);

            if (isset($_POST['note'])) {
                $note->setPerson($_SESSION['USER']);
                $note->setNote($_POST['note']);

                try {
                    $note->save();
                    $url = \Web\View::generateUrl('committees.applications', ['committee_id'=>$committee->getId()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new UpdateView($note);
        }

        return new \Web\Views\NotFoundView();
    }
}
