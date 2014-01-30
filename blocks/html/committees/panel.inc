<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Seat;
use Application\Models\VoteTable;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

include APPLICATION_HOME.'/blocks/html/committees/breadcrumbs.inc';
include APPLICATION_HOME.'/blocks/html/committees/info.inc';

$tabs = ['members', 'topics', 'votes', 'seats'];
$current_tab = (isset($_GET['tab']) && in_array($_GET['tab'], $tabs)) ? $_GET['tab'] : 'members';
	$block = new Block(
		'tabs.inc',
		['tabs'=>$tabs, 'current_tab'=>$current_tab, 'base_url'=>new Url($this->committee->getUrl())]
	);
	echo $block->render('html', $this->template);
switch ($current_tab) {
	case 'members':
		$f = (isset($_GET['members']) && $_GET['members']=='past') ? 'pastMembers.inc' : 'currentMembers.inc';
		include APPLICATION_HOME."/blocks/html/committees/$f";
		break;

	case 'topics':
		$order = !empty($_GET['sort']) ? $_GET['sort'] : null;

		$block = new Block(
			'topics/panel.inc',
			['topics'=>$this->committee->getTopics(null, $order), 'committee'=>$this->committee]
		);
		echo $block->render('html', $this->template);
		break;

	case 'votes':
		$search = ['committee_id'=>$this->committee->getId()];

		$table = new VoteTable();
		$votes = $table->find($search, null, true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$votes->setCurrentPageNumber($page);
		$votes->setItemCountPerPage(10);

		$people = array();
		foreach($this->committee->getCurrentTerms() as $term) {
			$people[] = $term->getPerson();
		}

		$votesList  = new Block('votes/list.inc',         ['votes'=>$votes]);
		$paginator  = new Block('pageNavigation.inc', ['paginator'=>$votes]);
		$comparison = new Block('votingRecords/comparisonPanel.inc', ['search'=>$search, 'people'=>$people]);
		echo $votesList ->render('html', $this->template);
		echo $paginator ->render('html', $this->template);
		echo $comparison->render('html', $this->template);

		break;

	case 'seats':
		$block = new Block(
			'seats/list.inc',
			['seats'=>$this->committee->getSeats(), 'committee'=>$this->committee]
		);
		echo $block->render('html', $this->template);

		if (!empty($_GET['seat_id'])) {
			try {
				$seat = new Seat($_GET['seat_id']);

				$seatInfo      = new Block('seats/info.inc', ['seat'=>$seat]);
				$currentTerms  = new Block('terms/list.inc', ['terms'=>$seat->getTerms(['current'=>time()]), 'title'=>$this->template->_('labels.current_terms')]);
				$pastTerms     = new Block('terms/list.inc', ['terms'=>$seat->getTerms(['before' =>time()]), 'title'=>$this->template->_('labels.past_terms')]);
				echo $seatInfo    ->render('html', $this->template);
				echo $currentTerms->render('html', $this->template);
				echo $pastTerms   ->render('html', $this->template);
			}
			catch (\Exception $e) {
				// Just ignore them if they try to ask for a seat that doesn't exist
			}
		}
		break;
}
