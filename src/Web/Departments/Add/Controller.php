<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Departments\Add;

use Application\Models\Department;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        $department = new Department();
        if (isset($_POST['name'])) {
            try {
                $department->handleUpdate($_POST);
                $department->save();
                header('Location: ' . View::generateUrl('departments.index'));
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }
        return new View($department);
    }
}