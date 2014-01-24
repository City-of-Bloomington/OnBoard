<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Requirement;
use Application\Models\RequirementTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class RequirementsController extends Controller
{
	public function index()
	{
		$table = new RequirementTable();
		$requirements = $table->find();

		$this->template->blocks[] = new Block('requirements/list.inc', ['requirements'=>$requirements]);
	}

	public function update()
	{
		if (!empty($_REQUEST['requirement_id'])) {
			try {
				$requirement = new Requirement($_REQUEST['requirement_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$requirement = new Requirement();
		}

		if (isset($_POST['text'])) {
			$requirement->setText($_POST['text']);
			try {
				$requirement->save();
				header('Location: '.BASE_URL.'/requirements');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('requirements/updateForm.inc', ['requirement'=>$requirement]);
	}

	public function delete()
	{
		try {
			$requirement = new Requirement($_GET['requirement_id']);
			$requirement->delete();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/requirements');
		exit();
	}
}
