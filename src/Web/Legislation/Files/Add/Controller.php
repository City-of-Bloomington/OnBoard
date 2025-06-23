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

                        header('Location: ').\Web\View::generateUrl('legislation.view', [
                            'legislation_id' => $file->getLegislation_id(),
                            'committee_id'   => $file->getLegislation()->getCommittee_id()
                        ]);
                        exit();
                    }
                    catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
                }
            }
            return new \Web\Legislation\Files\Update\View($file, $_SESSION['return_url']);
        }

        return new \Web\Views\NotFoundView();
    }
    /**
     * ACL will call this function before invoking the Controller
     *
     * When a role needs to check the Department Association, the ACL will
     * be checked before invoking the Controller.  This function must be called
     * statically.  The current route parameters will be passed.  These parameters
     * will be the same as would be passed to __invoke().
     *
     * @see Web\Auth\DepartmentAssociation
     * @see access_control.php
     */
    public static function hasDepartment(int $department_id, array $params): bool
    {
        if (!empty($params['id'])) {
            return LegislationFilesTable::hasDepartment($department_id, (int)$params['id']);
        }
        if (!empty($_REQUEST['legislation_id'])) {
            return LegislationTable::hasDepartment($department_id, (int)$_REQUEST['legislation_id']);
        }
        return false;
    }
}
