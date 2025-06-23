<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Files\Add;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Application\Models\Legislation\LegislationFile;
use Application\Models\Legislation\LegislationFilesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $file = new LegislationFile();

        if (!$file->getLegislation_id()) {
            if (!empty($_REQUEST['legislation_id'])) {
                try {
                    $l = new Legislation((int)$_REQUEST['legislation_id']);
                    $file->setLegislation($l);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
        }

        if ($file->getLegislation_id()) {
            if (isset($_POST['legislation_id'])) {
                if (isset($_FILES['legislationFile']) && $_FILES['legislationFile']['error'] != UPLOAD_ERR_NO_FILE) {
                    try {
                        $file->setFile($_FILES['legislationFile']);
                        $file->save();

                        $url = \Web\View::generateUrl('legislation.view', [
                            'legislation_id' => $file->getLegislation_id(),
                            'committee_id'   => $file->getLegislation()->getCommittee_id()
                        ]);
                        header("Location: $url");
                        exit();
                    }
                    catch (\Exception $e) {
                        $_SESSION['errorMessages'][] = $e->getMessage();
                    }
                }
            }
            return new \Web\Legislation\Files\Update\View($file);
        }

        return new \Web\Views\NotFoundView();
    }
}
