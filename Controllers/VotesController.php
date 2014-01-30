<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Application\Models\Vote;
use Application\Models\VoteTable;

class VotesController extends Controller
{
	public function __construct(\Blossom\Classes\Template $template)
	{
		if (!defined('VOTE_TRACKING') || !VOTE_TRACKING) {
			$_SESSION['errorMessages'][] = new \Exception('votes/notEnabled');
			header('Location: '.BASE_URL);
			exit();
		}
		else {
			parent::__construct($template);
		}
	}

	/**
	 * Tries to load and return the Vote
	 *
	 * Updates Sessions errorMessages with any problems
	 *
	 * @param int $id
	 * @return Vote
	 */
	private function loadVote($id)
	{
		try {
			$vote = new Vote($id);
			return $vote;
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/committees');
			exit();
		}
	}

	public function index()
	{	$table = new VoteTable();
		$votes = $table->find();

		$this->template->blocks[] = new Block('votes/list.inc', ['votes'=>$votes]);
	}

	public function view()
	{
		$vote = $this->loadVote($_GET['vote_id']);

		$topic = $vote->getTopic();

		$this->template->title = $this->template->_('labels.edit_votingRecords');

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$topic->getCommittee()]);
		$this->template->blocks[] = new Block('topics/info.inc', ['topic'=>$topic]);
		$this->template->blocks[] = new Block('votes/info.inc', ['vote'=>$vote]);

		if ($vote->hasVotingRecords()) {
			$this->template->blocks[] = new Block('votingRecords/list.inc', ['vote'=>$vote]);
		}
	}

	public function update()
	{
		// Updating an existing vote only requires a vote_id
		if (!empty($_REQUEST['vote_id'])) {
			$vote = $this->loadVote($_REQUEST['vote_id']);
		}
		// Adding a new vote requires a topic_id
		else {
			$vote = new Vote();
			if (!empty($_REQUEST['topic_id'])) {
				try {
					$vote->setTopic_id($_REQUEST['topic_id']);
				}
				catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
			}
			else {
				$_SESSION['errorMessages'][] = new \Exception('votes/missingTopic');
			}
			if (isset($_SESSION['errorMessages'])) {
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}

		// Process the form
		if (isset($_POST['voteType_id'])) {
			try {
				$vote->handleUpdate($_POST);
				$vote->save();
				header('Location: '.$vote->getTopic()->getUrl());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
		$this->template->title = $this->template->_('labels.edit_vote');
		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$vote->getCommittee()]);
		$this->template->blocks[] = new Block('topics/info.inc', ['topic'=>$vote->getTopic()]);
		$this->template->blocks[] = new Block('votes/updateForm.inc', ['vote'=>$vote]);
	}

	public function recordVotes()
	{
		$vote = $this->loadVote($_REQUEST['vote_id']);
		if (isset($_POST['votingRecords'])) {
			try {
				// This updates the database without needing to call ->save()
				$vote->setVotingRecords($_POST['votingRecords']);
				header('Location: '.$vote->getUrl());
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$vote->getCommittee()]);
		$this->template->blocks[] = new Block('topics/info.inc', ['topic'=>$vote->getTopic()]);
		$this->template->blocks[] = new Block('votes/info.inc', ['vote'=>$vote]);
		$this->template->blocks[] = new Block('votes/recordVotesForm.inc', ['vote'=>$vote]);
	}
}
