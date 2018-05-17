<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeStatute;
use Application\Models\CommitteeStatuteTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class CommitteeStatutesController extends Controller
{
    public function index()
    {
    }

    public function update()
    {
        if (!empty($_REQUEST['committeeStatute_id'])) {
            try { $statute = new CommitteeStatute($_REQUEST['committeeStatute_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            if (!empty($_REQUEST['committee_id'])) {
                try {
                    $committee = new Committee($_REQUEST['committee_id']);
                    $statute   = new CommitteeStatute();
                    $statute->setCommittee($committee);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }

        if (isset($statute)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $statute->handleUpdate($_POST);
                    $statute->save();
                    header('Location: '.BASE_URL.'/committees/info?committee_id='.$statute->getCommittee_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('committeeStatutes/updateForm.inc', ['committeeStatute'=>$statute]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_GET['id'])) {
            try { $statute = new CommitteeStatute($_GET['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($statute)) {
            try {
                $committee_id = $statute->getCommittee_id();
                $statute->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            header('Location: '.BASE_URL.'/committees/info?committee_id='.$committee_id);
            exit();
        }

        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks[] = new Block('404.inc');
    }
}
