<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Race;
use Application\Models\RaceTable;

use Web\Controller;
use Web\Block;
use Web\View;

class RacesController extends Controller
{
	public function index(): View
	{
		$table = new RaceTable();
		$races = $table->find();

		$title = $this->template->_(['race', 'races', count($races)]);
		$this->template->title = $title.' - '.APPLICATION_NAME;
		$this->template->blocks[] = new Block('races/list.inc', ['races'=>$races]);
        return $this->template;
	}

	public function update(): View
	{
        $return_url = View::generateUrl('races.index');
		if (!empty($_REQUEST['race_id'])) {
			try {
				$race = new Race($_REQUEST['race_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $return_url");
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
				header("Location: $return_url");
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('races/updateForm.inc', ['race'=>$race]);
        return $this->template;
	}
}
