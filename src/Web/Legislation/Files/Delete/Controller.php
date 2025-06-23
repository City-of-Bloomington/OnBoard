<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Files\Delete;

use Application\Models\Legislation\LegislationFile;
use Application\Models\Legislation\LegislationFilesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislationFile_id'])) {
            try {
                $file = new LegislationFile((int)$_REQUEST['legislationFile_id']);
                $file->delete();
                header('Location: '.\Web\View::generateUrl('legislation.view', [
                    'legislation_id' => $file->getLegislation_id(),
                    'committee_id'   => $file->getLegislation()->getCommittee_id()
                ]));
                exit();
            }
            catch (\Exception $e) { }
        }
        return new \Web\Views\NotFoundView();
    }
}
