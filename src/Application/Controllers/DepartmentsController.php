<?php
/**
 * @copyright 2016-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Department;
use Application\Models\DepartmentTable;

use Web\Controller;
use Web\Block;
use Web\View;

class DepartmentsController extends Controller
{
	public function index(): View
	{
        $table = new DepartmentTable();
        $list = $table->find();

        $title = $this->template->_(['department', 'departments', count($list)]);
        $this->template->title = $title.' - '.APPLICATION_NAME;
        $this->template->blocks[] = new Block('departments/list.inc', ['departments'=>$list]);
        return $this->template;
	}

	public function view(): View
    {
        if (!empty($_REQUEST['department_id'])) {
            try { $department = new Department($_REQUEST['department_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($department)) {
            $this->template->title  = $department->getName().' - '.APPLICATION_NAME;
            $this->template->blocks = [
                new Block('departments/info.inc', ['department' => $department])
            ];
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }

	public function update(): View
	{
        if (!empty($_REQUEST['department_id'])) {
            try { $department = new Department($_REQUEST['department_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $department = new Department();
        }

        if (isset($department)) {
            if (isset($_POST['name'])) {
                try {
                    $department->handleUpdate($_POST);
                    $department->save();
                    $return_url = View::generateUrl('departments.index');
                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('departments/updateForm.inc', ['department'=>$department]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
	}
}
