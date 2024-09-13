<?php
declare(strict_types=1);
namespace Web\Departments\Update;

use Application\Models\Department;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['department_id'])) {
            try { $department = new Department($_REQUEST['department_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        else {
            $department = new Department();
        }

        if (isset($department)) {
            if (isset($_POST['name'])) {
                try {
                    $department->handleUpdate($_POST);
                    $department->save();
                    $return_url = \Web\View::generateUrl('departments.index');
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            return new View($department);
        }
        return new \Web\Views\NotFoundView();
    }
}
