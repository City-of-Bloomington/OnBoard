<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Files\Update;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationFile;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislationFile_id'])) {
            try { $file = new LegislationFile((int)$_REQUEST['legislationFile_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else { $file = new LegislationFile(); }

        if (!$file->getLegislation_id()) {
            if (!empty($_REQUEST['legislation_id'])) {
                try {
                    $l = new Legislation((int)$_REQUEST['legislation_id']);
                    $file->setLegislation($l);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }
        }

        $_SESSION['return_url'] = !empty($_REQUEST['return_url'])
                                ? $_REQUEST['return_url']
                                : ($file->getLegislation_id()
                                    ? \Web\View::generateUrl('legislation.view').'?legislation_id='.$file->getLegislation_id()
                                    : \Web\View::generateUrl('legislation.index'));


        if (isset($file) && $file->getLegislation_id()) {
            if (isset($_POST['legislation_id'])) {
                if (isset($_FILES['legislationFile']) && $_FILES['legislationFile']['error'] != UPLOAD_ERR_NO_FILE) {
                    try {
                        $file->setFile($_FILES['legislationFile']);
                        $file->save();

                        $return_url = $_SESSION['return_url'];
                        unset($_SESSION['return_url']);
                        header("Location: $return_url");
                        exit();
                    }
                    catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
                }
            }

            return new View($file, $_SESSION['return_url']);
        }

        return new \Web\Views\NotFoundView();
    }
}