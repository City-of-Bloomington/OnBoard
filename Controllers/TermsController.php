<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Seat;
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
        // Loading
        if (!empty($_REQUEST['term_id'])) {
            try {
                $term = new Term($_REQUEST['term_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
                $term = new Term();
                $term->setSeat($seat);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        // Handling the POST
        if (isset($term)) {
            if (isset($_POST['seat_id'])) {
                try {
                    $term->handleUpdate($_POST);
                    $term->save();
                    header('Location: '.BASE_URL.'/seats/view?seat_id='.$term->getSeat_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $seat = $term->getSeat();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee'=>$seat->getCommittee()]);
            $this->template->blocks[] = new Block('seats/info.inc',       ['seat' => $seat]);
            $this->template->blocks[] = new Block('terms/updateForm.inc', ['term' => $term]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

	public function generate()
	{
        if (!empty($_REQUEST['term_id'])) {
            try { $term = new Term($_REQUEST['term_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (   empty($_REQUEST['direction'])) { $_REQUEST['direction'] = 'next'; }
        $generator = $_REQUEST['direction'] === 'next'
            ? 'generateNextTerm'
            : 'generatePreviousTerm';

        if (isset($term)) {
            $newTerm = $term->$generator();
            try { $newTerm->save(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            header('Location: '.BASE_URL.'/seats/view?seat_id='.$term->getSeat_id());
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

}
