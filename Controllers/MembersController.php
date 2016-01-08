<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Member;
use Application\Models\Seat;
use Application\Models\Term;
use Application\Models\TermTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class MembersController extends Controller
{
    public function index() { }

    public function update()
    {
        if (isset($_REQUEST['member_id'])) {
            try { $member = new Member($_REQUEST['member_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (isset($_REQUEST['term_id'])) {
            $term = new Term($_REQUEST['term_id']);
            $member = new Member();
            $member->setTerm($term);
            $member->setSeat($term->getSeat());
            $member->setCommittee($term->getCommittee());
        }
        elseif (isset($_REQUEST['seat_id'])) {
            $seat = new Seat($_REQUEST['seat_id']);
            $member = new Member();
            $member->setSeat($seat);
            $member->setCommittee($seat->getCommittee());
        }
        elseif (isset($_REQUEST['committee_id'])) {
            $committee = new Committee($_REQUEST['committee_id']);
            $member = new Member();
            $member->setCommittee($committee);
        }

        if (isset($member)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $member->handleUpdate($post);
                    $member->save();
                    header('Location: '.BASE_URL."/committees/view?committee_id={$member->getCommittee_id()}");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = $member->getSeat_id()
                ? new Block('seats/panel.inc',      ['seat'      => $member->getSeat()])
                : new Block('committees/panel.inc', ['committee' => $member->getCommittee()]);

            if ($member->getTerm_id()) {
                $this->template->blocks[] = new Block('members/list.inc', ['members'=>$member->getTerm()->getMembers()]);
            }

            $this->template->blocks[] = new Block('members/updateForm.inc', ['member'=>$member]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}