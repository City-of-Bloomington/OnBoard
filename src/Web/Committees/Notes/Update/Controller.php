<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Notes\Update;

use Application\Models\Committees\Note;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['note_id'])) {
            try { $note = new Note($_REQUEST['note_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($note)) {
            if (isset($_POST['note'])) {
                $note->setPerson($_SESSION['USER']);
                $note->setNote($_POST['note']);

                try {
                    $note->save();
                    $url = \Web\View::generateUrl('committees.applications', ['committee_id'=>$note->getCommittee_id()]);
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
            return new View($note);
        }
        return \Web\Views\NotFoundView();
    }
}
