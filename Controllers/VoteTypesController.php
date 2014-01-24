<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Application\Models\VoteType;
use Application\Models\VoteTypeTable;

class VoteTypesController extends Controller
{
	public function index()
	{
		$table = new VoteTypeTable();
		$list = $table->find();
		$this->template->blocks[] = new Block('voteTypes/list.inc', ['voteTypes'=>$list]);
	}

	public function update()
	{
		if (!empty($_REQUEST['voteType_id'])) {
			try {
				$voteType = new VoteType($_REQUEST['voteType_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$voteType = new VoteType();
		}

		if (isset($_POST['name'])) {
			try {
				$voteType->handleUpdate($_POST);
				$voteType->save();
				header('Location: '.BASE_URL.'/voteTypes');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('voteTypes/updateForm.inc', ['voteType'=>$voteType]);
	}
}
