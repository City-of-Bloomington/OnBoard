<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        if (!empty($params['id'])) {
            try {
                $file = new LegislationFile((int)$params['id']);
                $lid  = $file->getLegislation_id();
                $url  = \Web\View::generateUrl('legislation.view', ['id'=>$lid]);

                $file->delete();
                header("Location: $url");
                exit();
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
        return !empty($params['id'])
            && LegislationFilesTable::hasDepartment($department_id, (int)$params['id']);
    }
}
