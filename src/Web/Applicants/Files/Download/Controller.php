<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applicants\Files\Download;

use Application\Models\ApplicantTable;
use Application\Models\ApplicantFile;
use Application\Models\ApplicantFilesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_REQUEST['applicantFile_id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { }
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
        if (!empty($_REQUEST['applicantFile_id'])) {
            return ApplicantFilesTable::hasDepartment($department_id, (int)$_REQUEST['applicantFile_id']);
        }
        return false;
    }
}
