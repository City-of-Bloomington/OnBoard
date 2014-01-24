<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Tag;
use Application\Models\TagTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class TagsController extends Controller
{
	private function loadTag($id)
	{
		try {
			$tag = new Tag($id);
			return $tag;
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/tags');
			exit();
		}
	}
	public function index()
	{
		$table = new TagTable();
		$tags = $table->find();

		$this->template->blocks[] = new Block('tags/breadcrumbs.inc');
		$this->template->blocks[] = new Block('tags/list.inc', ['tags'=>$tags]);
	}

	public function view()
	{
		$tag = $this->loadTag($_GET['tag_id']);

		$this->template->blocks[] = new Block('tags/breadcrumbs.inc', ['tag'=>$tag]);
		$this->template->blocks[] = new Block('tags/info.inc', ['tag'=>$tag]);
		$this->template->blocks[] = new Block('topics/list.inc', ['topics'=>$tag->getTopics()]);
	}

	public function update()
	{
		if (!empty($_REQUEST['tag_id'])) {
			$tag = $this->loadTag($_REQUEST['tag_id']);
		}
		else {
			$tag = new Tag();
		}

		if (isset($_POST['name'])) {
			$tag->setName($_POST['name']);
			try {
				$tag->save();
				header('Location: '.BASE_URL.'/tags');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('tags/breadcrumbs.inc', ['tag'=>$tag]);
		$this->template->blocks[] = new Block('tags/updateForm.inc',  ['tag'=>$tag]);
	}

	public function delete()
	{
		try {
			$tag = new Tag($_GET['tag_id']);
			$tag->delete();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/tags');
		exit();
	}
}
