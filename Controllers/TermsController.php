<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Term;
use Application\Models\TermTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class TermsController extends Controller
{
	public function index()
	{
		$table = new TermTable();
		$terms = $table->find();
		$this->template->blocks[] = new Block('terms/list.inc', ['terms'=>$terms]);
	}

	public function update()
	{
		// To Add a term, you need a seat_id
		if (empty($_REQUEST['term_id'])) {
			$term = new Term();

			if (!empty($_REQUEST['committee_id'])) {
                try {
                    $term->setCommittee_id($_REQUEST['committee_id']);
                }
                catch (\Exception $e) {
                    $requiredFieldMissing = true;
                    $_SESSION['errorMessages'][] = $e;
                }
			}

			if (!empty($_REQUEST['seat_id'])) {
				try {
					$term->setSeat_id($_REQUEST['seat_id']);
				}
				catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
			}

            // If we don't have a committee and/or seat, we should error out right now
			$committee_id = $term->getCommittee_id();
			if (   !$committee_id
                || ($term->getCommittee()->getType() === 'seated' && !$term->getSeat_id())) {

                if (!empty($_REQUEST['return_url'])) { $r = $_REQUEST['return_url']; }
                else {
                    $r = BASE_URL.'/committees';
                    if ($committee_id) { $r .="/view?committee_id=$committee_id"; }
                }
                header("Location: $r");
                exit();
            }


			if (!empty($_REQUEST['person_id'])) {
				try {
					$term->setPerson_id($_REQUEST['person_id']);
				}
				catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
			}
		}
		// To update an existing term, all you need is the term_id
		else {
			try {
				$term = new Term($_REQUEST['term_id']);
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		if (isset($term)) {
			$return_url = !empty($_REQUEST['return_url'])
				? $_REQUEST['return_url']
				: $term->getSeat()->getUrl();

			if (isset($_POST['term_start'])) {
				try {
					$term->handleUpdate($_POST);

					// If there are invalid votingRecords, make sure the user knows they're
					// going to be deleting a bunch of votingRecords when they save
					if ($term->hasInvalidVotingRecords()) {
						$_SESSION['pendingTerm'] = $term;
						header('Location: '.BASE_URL.'/terms/confirmDeleteInvalidVotingRecords');
						exit();
					}
					$term->save();
					header('Location: '.$return_url);
					exit();
				}
				catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
			}

			$this->template->blocks[] = new Block('committees/info.inc',  ['committee'=>$term->getCommittee()]);
			$seat = $term->getSeat();
			if ($seat) {
                $this->template->blocks[] = new BlocK('seats/info.inc',   ['seat'=>$seat]);
            }
			$this->template->blocks[] = new Block('terms/updateForm.inc', ['term'=>$term, 'return_url'=>$return_url]);
		}
		else {
			header('Location: '.BASE_URL."/committees");
			exit();
		}
	}

	public function delete()
	{
		$term = new Term($_REQUEST['term_id']);
		$seat = $term->getSeat();

		$term->delete();
		header('Location: '.$seat->getUrl());
		exit();
	}

	public function confirmDeleteInvalidVotingRecords()
	{
		if (isset($_GET['confirm'])) {
			try {
				$_SESSION['pendingTerm']->save();
			}
			catch (\Exception $e) {
				header('Location: '.BASE_URL.'/terms/update?term_id='.$_SESSION['pendingTerm']->getId());
				exit();
			}
			unset($_SESSION['pendingTerm']);
			header('Location: '.$term->getSeat()->getUrl());
			exit();
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$_SESSION['pendingTerm']->getCommittee()->getId()]);
		$this->template->blocks[] = new Block('seats/info.inc', ['seat'=>$_SESSION['pendingTerm']->getSeat()]);
		$this->template->blocks[] = new Block('terms/confirmDeleteInvalidVotingRecords.inc', ['term'=>$_SESSION['pendingTerm']]);
	}
}
