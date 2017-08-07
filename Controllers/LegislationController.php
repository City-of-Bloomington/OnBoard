<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class LegislationController extends Controller
{
    public function index()
    {
        $_GET['parent_id'] = null;

        $table = new LegislationTable();
        $vars  = ['list' => $table->find($_GET)];

        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
        }

        if ($this->template->outputFormat == 'html') {
            if (isset($committee)) {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
                $vars['committee'] = $committee;
            }
            $this->template->blocks[] = new Block('legislation/searchForm.inc');
        }

        $this->template->blocks[] = new Block('legislation/list.inc', $vars);
    }

    public function view()
    {
        try { $legislation = new Legislation($_GET['id']); }
        catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }

        if (isset($legislation)) {
            $committee = $legislation->getCommittee();
            if ($this->template->outputFormat === 'html') {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
            }

            $this->template->blocks[] = new Block('legislation/info.inc', ['legislation'=>$legislation]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $legislation = new Legislation($_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
        }
        else { $legislation = new Legislation(); }


        if (isset($legislation)) {
            if (!$legislation->getCommittee_id()) {
                if (!empty($_REQUEST['committee_id'])) {
                    try { $legislation->setCommittee_id($_REQUEST['committee_id']); }
                    catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
                }
            }

            if (isset($_REQUEST['parent_id'])) {
                try {
                    $parent = new Legislation($_REQUEST['parent_id']);
                    $legislation->setParent_id   ($parent->getId());
                    $legislation->setCommittee_id($parent->getCommittee_id());
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }

            if (isset($_REQUEST['type_id'])) {
                try { $legislation->setType_id($_REQUEST['type_id']); }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }
        }

        if (isset($legislation) && $legislation->getCommittee_id()) {
            $_SESSION['return_url'] =    !empty($_REQUEST['return_url'])
                                    ? urldecode($_REQUEST['return_url'])
                                    : ($legislation->getId()
                                        ? BASE_URL.'/legislation/view?id='.$legislation->getId()
                                        : BASE_URL.'/legislation');

            if (isset($_POST['number'])) {
                try {
                    $legislation->handleUpdate($_POST);
                    # Legislation::handleUpdate calls save automatically

                    $return_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $legislation->getCommittee()]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $legislation->getCommittee()]);
            $this->template->blocks[] = new Block('legislation/updateForm.inc', ['legislation'=>$legislation]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
