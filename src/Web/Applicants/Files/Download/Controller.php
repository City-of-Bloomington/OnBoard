<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        if (!empty($_GET['applicantFile_id'])) {
            try {
                $file = new ApplicantFile($_GET['applicantFile_id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) { }
        }
        return new \Web\Views\NotFoundView();
    }

    /**
     * ACL will call this function when a role needs to check the Department Association
     *
     * @see Web\Auth\DepartmentAssociation
     */
    public static function hasDepartment(int $department_id): bool
    {
        if (!empty($_GET['applicant_id'])) {
            return ApplicantTable::hasDepartment($department_id, (int)$_GET['applicant_id']);
        }
        if (!empty($_GET['applicantFile_id'])) {
            return ApplicantFilesTable::hasDepartment($department_id, (int)$_REQUEST['applicantFile_id']);
        }
        return false;
    }
}
