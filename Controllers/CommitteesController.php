<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\Seat;
use Application\Models\VoteTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

class CommitteesController extends Controller
{
	public function index()
	{
		$table = new CommitteeTable();
		$committees = $table->find();
		$this->template->blocks[] = new Block('committees/breadcrumbs.inc');
		$this->template->blocks[] = new Block('committees/list.inc', ['committees'=>$committees]);
	}

	public function view()
	{
		try {
			$committee = new Committee($_GET['committee_id']);
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/committees');
			exit();
		}

		// Web service calls ony get a limited amount of information
		if ($this->template->outputFormat != 'html') {
			$this->template->blocks[] = new Block('committees/info.inc',           ['committee'=>$committee]);
			$this->template->blocks[] = new Block('committees/currentMembers.inc', ['committee'=>$committee]);
		}
		// HTML pages are more interactive
		else {
			$this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee'=>$committee]);
			$this->template->blocks[] = new Block('committees/info.inc',        ['committee'=>$committee]);

			$tabs = ['members', 'topics', 'votes', 'seats'];
			$current_tab = (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) ? $_GET['tab'] : 'members';
			if ($this->template->outputFormat == 'html') {
				$this->template->blocks[] = new Block(
					'tabs.inc',
					['tabs'=>$tabs,'current_tab'=>$current_tab,'base_url'=>new Url($committee->getURL())]
				);
			}
			switch ($current_tab) {
				case 'members':
					$block = (isset($_GET['members']) && $_GET['members']=='past')
							? 'pastMembers.inc'
							: 'currentMembers.inc';
					$this->template->blocks[] = new Block("committees/$block", ['committee'=>$committee]);
					break;

				case 'topics':
					$order = !empty($_GET['sort']) ? $_GET['sort'] : null;

					$this->template->blocks[] = new Block(
						'topics/panel.inc',
						['topics'=>$committee->getTopics(null, $order), 'committee'=>$committee]
					);
					break;

				case 'votes':
					$search = ['committee_id'=>$committee->getId()];

					$table = new VoteTable();
					$votes = $table->find($search, null, true);

					$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
					$votes->setCurrentPageNumber($page);
					$votes->setItemCountPerPage(10);

					$this->template->blocks[] = new Block('votes/list.inc', ['votes'=>$votes]);
					$this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$votes]);

					$people = array();
					foreach($committee->getCurrentTerms() as $term) {
						$people[] = $term->getPerson();
					}
					$this->template->blocks[] = new Block('votingRecords/comparisonPanel.inc', ['search'=>$search, 'people'=>$people]);
					break;

				case 'seats':
					$this->template->blocks[] = new Block(
						'seats/list.inc',
						['seats'=>$committee->getSeats(), 'committee'=>$committee]
					);
					if (!empty($_GET['seat_id'])) {
						try {
							$seat = new Seat($_GET['seat_id']);
							$this->template->blocks[] = new Block('seats/info.inc', ['seat'=>$seat]);
							$this->template->blocks[] = new Block('terms/list.inc', ['terms'=>$seat->getTerms(['current'=>time()]), 'title'=>$this->template->_('labels.current_terms')]);
							$this->template->blocks[] = new Block('terms/list.inc', ['terms'=>$seat->getTerms(['before' =>time()]), 'title'=>$this->template->_('labels.past_terms')]);
						}
						catch (\Exception $e) {
							// Just ignore them if they try to ask for a seat that doesn't exist
						}
					}
					break;
			}
		}
	}

	public function update()
	{
		if (!empty($_REQUEST['committee_id'])) {
			try {
				$committee = new Committee($_REQUEST['committee_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}
		else {
			$committee = new Committee();
		}

		if (isset($_POST['name'])) {
			try {
				$committee->handleUpdate($_POST);
				$committee->save();
				header('Location: '.$committee->getUrl());
				exit();
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		$this->template->blocks[] = new Block('committees/updateForm.inc', ['committee'=>$committee]);
	}
}
