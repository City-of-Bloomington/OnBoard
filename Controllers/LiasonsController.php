<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

class LiasonsController extends Controller
{
    public function index() { }

    public function add()
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($committee)) {
            if (isset($_POST['person_id'])) {
                try {
                    $committee->saveLiason($_POST);
                    header('Location: '.BASE_URL.'/committees/info?committee_id='.$committee->getId());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
            $this->template->blocks[] = new Block('liasons/addForm.inc',        ['committee' => $committee]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function remove()
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($committee)) {
            if (!empty($_REQUEST['person_id'])) {
                try {
                    $committee->removeLiason($_REQUEST['person_id']);
                    header('Location: '.BASE_URL.'/committees/info?committee_id='.$committee->getId());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}