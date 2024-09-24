<?php
/**
 * @copyright 2016-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeStatute;
use Application\Models\CommitteeStatuteTable;

use Web\Controller;
use Web\Block;
use Web\View;

class CommitteeStatutesController extends Controller
{
    public function update(): View
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
                    $return_url = View::generateUrl('committees.info').'?committee_id='.$statute->getCommittee_id();
                    header("Location: $return_url");
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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_GET['committeeStatute_id'])) {
            try { $statute = new CommitteeStatute($_GET['committeeStatute_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($statute)) {
            try {
                $committee_id = $statute->getCommittee_id();
                $statute->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            $return_url = View::generateUrl('committees.info').'?committee_id='.$committee_id;
            header("Location: $return_url");
            exit();
        }

        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks[] = new Block('404.inc');
        return $this->template;
    }
}
