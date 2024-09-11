<?php
declare(strict_types=1);
namespace Web\Departments\Info;

use Application\Models\Department;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $department_id = $_GET['department_id'] ?? null;

        if (!$department_id) {
            $_SESSION['errorMessages'][] = 'No department specified';
            header('Location: ' . \Web\View::generateUrl('departments.index'));
            exit();
        }

        try {
            $department = new Department($department_id);
            return new View($department);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            header('Location: ' . \Web\View::generateUrl('departments.index'));
            exit();
        }
    }
}