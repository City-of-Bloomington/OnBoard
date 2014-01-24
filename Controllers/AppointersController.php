<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Appointer;
use Application\Models\AppointerTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class AppointersController extends Controller
{
	public function index()
	{
		$table = new AppointerTable();
		$appointers = $table->find();

		$this->template->blocks[] = new Block('appointers/list.inc', ['appointers'=>$appointers]);
	}

	public function update()
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
	}
}
