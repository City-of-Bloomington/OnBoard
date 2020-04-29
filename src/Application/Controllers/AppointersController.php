<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Appointer;
use Application\Models\AppointerTable;

use Web\Controller;
use Web\Block;
use Web\View;

class AppointersController extends Controller
{
	public function index(): View
	{
		$table = new AppointerTable();
		$appointers = $table->find();

		$title = $this->template->_(['appointer', 'appointers', count($appointers)]);
		$this->template->title = $title.' - '.APPLICATION_NAME;
		$this->template->blocks[] = new Block('appointers/list.inc', ['appointers'=>$appointers]);
        return $this->template;
	}

	public function update(): View
	{
		if (!empty($_REQUEST['appointer_id'])) {
			try {
				$appointer = new Appointer($_REQUEST['appointer_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$appointer = new Appointer();
		}

		if (isset($_POST['name'])) {
			$appointer->setName($_POST['name']);
			try {
				$appointer->save();
				header('Location: '.BASE_URL.'/appointers');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('appointers/updateForm.inc', ['appointer'=>$appointer]);
        return $this->template;
	}
}
