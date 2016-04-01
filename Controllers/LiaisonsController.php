<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Liaison;
use Application\Models\LiaisonTable;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

class LiaisonsController extends Controller
{
    public function index()
    {
        $type = (!empty($_GET['type']) && in_array($_GET['type'], Liaison::$types))
            ? $_GET['type']
            : Liaison::$types[0];

        $data  = LiaisonTable::data(['type'=>$type, 'current'=>true]);
        $title = $this->template->_(['liaison', 'liaisons', count($data['results'])]);

        $this->template->title = $title.' - '.APPLICATION_NAME;
        $this->template->blocks[] = new Block('liaisons/list.inc', ['data'=>$data]);
    }

    public function add()
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($committee)) {
            if (isset($_POST['person_id'])) {
                try {
                    $committee->saveLiaison($_POST);
                    header('Location: '.BASE_URL.'/committees/info?committee_id='.$committee->getId());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
            $this->template->blocks[] = new Block('liaisons/addForm.inc',        ['committee' => $committee]);
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
                    $committee->removeLiaison($_REQUEST['person_id']);
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