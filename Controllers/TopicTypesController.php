<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\TopicType;
use Application\Models\TopicTypeTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class TopicTypesController extends Controller
{
	public function index()
	{
		$table = new TopicTypeTable();
		$topicTypes = $table->find();

		$this->template->blocks[] = new Block('topicTypes/list.inc', ['topicTypes'=>$topicTypes]);
	}

	public function update()
	{
		if (!empty($_REQUEST['topicType_id'])) {
			try {
				$topicType = new TopicType($_REQUEST['topicType_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$topicType = new TopicType();
		}

		if (isset($_POST['name'])) {
			$topicType->setName($_POST['name']);
			try {
				$topicType->save();
				header('Location: '.BASE_URL.'/topicTypes');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('topicTypes/updateForm.inc', ['topicType'=>$topicType]);
	}
}
