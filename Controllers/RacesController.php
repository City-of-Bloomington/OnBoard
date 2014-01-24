<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Race;
use Application\Models\RaceTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class RacesController extends Controller
{
	public function index()
	{
		$table = new RaceTable();
		$races = $table->find();

		$this->template->blocks[] = new Block('races/list.inc', ['races'=>$races]);
	}

	public function update()
	{
		if (!empty($_REQUEST['race_id'])) {
			try {
				$race = new Race($_REQUEST['race_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$race = new Race();
		}

		if (isset($_POST['name'])) {
			$race->setName($_POST['name']);
			try {
				$race->save();
				header('Location: '.BASE_URL.'/races');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('races/updateForm.inc', ['race'=>$race]);
	}
}
