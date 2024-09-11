<?php
declare(strict_types=1);
namespace Web\Departments\Update;

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
        } catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            header('Location: ' . \Web\View::generateUrl('departments.index'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            try {
                $department->setName($_POST['name']);
                $department->save();
                $_SESSION['successMessages'][] = 'Department successfully updated';
                header('Location: ' . \Web\View::generateUrl('departments.index'));
                exit();
            } catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($department);
    }
}