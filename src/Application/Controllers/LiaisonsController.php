<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Person;
use Application\Models\Liaison;
use Application\Models\LiaisonTable;

use Web\Controller;
use Web\Block;
use Web\Url;

class LiaisonsController extends Controller
{
    public function index()
    {
        $type = (!empty($_GET['type']) && in_array($_GET['type'], Liaison::$types))
            ? $_GET['type']
            : Liaison::$types[0];

        $data  = LiaisonTable::data(['type'=>$type, 'current'=>true]);
        $title = $this->template->_(['liaison', 'liaisons', count($data['results'])]);

        if (!empty($_GET['format']) && $_GET['format'] === 'email') {
            $this->template->setOutputFormat('txt');
        }

        $this->template->title = $title.' - '.APPLICATION_NAME;
        $this->template->blocks[] = new Block('liaisons/list.inc', ['data'=>$data]);
    }

    public function update()
    {
        if (!empty($_REQUEST['liaison_id'])) {
            try {
                $liaison = new Liaison($_REQUEST['liaison_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $liaison   = new Liaison();
                $liaison->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($liaison)) {
            if (!empty($_REQUEST['person_id'])) {
                try {
                    $person = new Person($_REQUEST['person_id']);
                    $liaison->setPerson($person);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            if (isset($_POST['person_id'])) {
                try {
                    $liaison->handleUpdate($_POST);
                    $liaison->save();

                    header('Location: '.BASE_URL.'/committees/info?committee_id='.$liaison->getCommittee_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $liaison->getCommittee()]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $liaison->getCommittee()]);
            $this->template->blocks[] = new Block('liaisons/updateForm.inc',    ['liaison'   => $liaison]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_REQUEST['liaison_id'])) {
            try {
                $liaison = new Liaison($_REQUEST['liaison_id']);
                $committee_id = $liaison->getCommittee_id();

                try { $liaison->delete(); }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

                header('Location: '.BASE_URL.'/committees/info?committee_id='.$committee_id);
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
