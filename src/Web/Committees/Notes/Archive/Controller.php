<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Notes\Archive;

use Application\Models\Committees\Note;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            $note = new Note($params['note_id']);
            $note->setArchived(new \Datetime());
            $note->save();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
        }

        $url = \Web\View::generateUrl('committees.applications', ['committee_id'=>$params['committee_id']]);
        header("Location: $url");
        exit();
    }
}
