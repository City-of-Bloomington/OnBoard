<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Department;
use Application\Models\DepartmentTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class DepartmentsController extends Controller
{
	public function index()
	{
        $table = new DepartmentTable();
        $list = $table->find();

        $title = $this->template->_(['department', 'departments', count($list)]);
        $this->template->title = $title.' - '.APPLICATION_NAME;
        $this->template->blocks[] = new Block('departments/list.inc', ['departments'=>$list]);
	}

	public function update()
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
                    header('Location: '.BASE_URL.'/departments');
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
	}
}