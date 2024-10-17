<?php
declare(strict_types=1);
namespace Web\Departments\Update;

use Application\Models\Department;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $department = new Department($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($department)) {
            if (isset($_POST['name'])) {
                $department->setName($_POST['name']);
                try {
                    $department->save();
                    header('Location: '.\Web\View::generateUrl('departments.index'));
                    exit();
                }
                catch (\Exception $e) { 
                    $_SESSION['errorMessages'][] = $e->getMessage();
                }
            }
            return new View($department);
        }
        
        return new \Web\Views\NotFoundView();
    }
}
