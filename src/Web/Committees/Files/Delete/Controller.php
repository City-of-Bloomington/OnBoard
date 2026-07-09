<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Files\Delete;

use Application\Models\CommitteeFile;
use Application\Models\CommitteeFileTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['file_id'])) {
            try {
                $file = new CommitteeFile($_REQUEST['file_id']);
                $url  = \Web\View::generateUrl('committees.files.index', ['committee_id'=>$file->getCommittee_id()]);

                $file->delete();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new \Web\Views\NotFoundView();
    }
}
