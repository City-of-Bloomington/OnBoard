<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Application\Models\Topic;
use Application\Models\TopicTable;

class TopicsController extends Controller
{
	/**
	 * Tries to load and return the Topic
	 *
	 * Updates Sessions errorMessages with any problems
	 *
	 * @param int $id
	 * @return Topic
	 */
	private function loadTopic($id)
	{
		try {
			$topic = new Topic($id);
			return $topic;
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/topics');
			exit();
		}
	}
	public function index()
	{
		$this->template->blocks[] = new Block('topics/breadcrumbs.inc');
		$this->template->blocks[] = new Block('topics/panel.inc');
	}

	public function view()
	{
		$topic = $this->loadTopic($_GET['topic_id']);

		$this->template->title = $topic->getDescription();
		$this->template->blocks[] = new Block('topics/breadcrumbs.inc', ['topic'=>$topic]);
		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$topic->getCommittee()]);
		$this->template->blocks[] = new Block('topics/info.inc', ['topic'=>$topic]);
		$this->template->blocks[] = new Block('tags/cloud.inc', ['search'=>['topic_id'=>$topic->getId()]]);
		if (defined('VOTE_TRACKING') && VOTE_TRACKING) {
			$this->template->blocks[] = new Block('votes/list.inc', ['topic'=>$topic, 'votes'=>$topic->getVotes()]);
			foreach ($topic->getVotes() as $vote) {
				$this->template->blocks[] = new Block('votingRecords/list.inc', ['vote'=>$vote]);
			}
		}
	}

	public function update()
	{
		// Updating a topic only requires the topic_id
		if (!empty($_REQUEST['topic_id'])) {
			$topic = $this->loadTopic($_REQUEST['topic_id']);
		}
		// Adding a new topic requires the committee_id
		else {
			if (empty($_REQUEST['committee_id'])) {
				$_SESSION['errorMessages'][] = new \Exception('topics/missingCommittee');
			}
			try {
				$topic = new Topic();
				$topic->setCommittee_id($_REQUEST['committee_id']);
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

			if (isset($_SESSION['errorMessages'])) {
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}

		// Process the form
		if (isset($_POST['topicType_id'])) {
			try {
				$topic->handleUpdate($_POST);
				$topic->save();
				header('Location: '.$topic->getUrl());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$topic->getCommittee()]);
		$this->template->blocks[] = new Block('topics/updateForm.inc', ['topic'=>$topic]);
	}
}
