<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Application\Models\Legislation\Status;
use Application\Models\Legislation\Type;

use Web\Controller;
use Web\Block;
use Web\View;

class LegislationController extends Controller
{
    public function index(): View
    {
        $_GET['parent_id'] = null;
        foreach (['type', 'status'] as $f) {
            if (!empty($_GET[$f]) && empty($_GET["{$f}_id"])) {
                try {
                    $class = '\\Application\\Models\Legislation\\'.ucfirst($f);
                    $$f    = new $class($_GET[$f]);
                    $_GET["{$f}_id"] = $$f->getId();
                }
                catch (\Exception $e) { }
                unset($_GET[$f]);
            }
        }

        $table = new LegislationTable();

        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
        }

        if ($this->template->outputFormat == 'html') {
            $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
            $list  = $table->find($_GET, 'year desc, number desc', true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(20);
            $vars  = ['list'=>$list];

            if (isset($committee)) {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
                $vars['committee'] = $committee;
            }
            $this->template->blocks[] = new Block('legislation/searchForm.inc');
            $this->template->blocks[] = new Block('legislation/list.inc', $vars);
            $this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$list]);
        }
        else {
            $this->template->blocks[] = new Block('legislation/list.inc', [
                'list' => $table->find($_GET)
            ]);
        }
        return $this->template;
    }

    public function years(): View
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);

                if ($this->template->outputFormat == 'html') {
                    $this->template->title = $committee->getName();
                    $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
                    $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
                }

                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $table = new LegislationTable();
        $years = $table->years($search);
        $this->template->blocks[] = new Block('legislation/years.inc', [
            'years'     => $years,
            'committee' => isset($committee) ? $committee : null
        ]);
        return $this->template;
    }

    public function view(): View
    {
        try { $legislation = new Legislation($_GET['legislation_id']); }
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
        return $this->template;
    }

    public function update(): View
    {
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
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

            if (!empty($_REQUEST['parent_id'])) {
                try {
                    $parent = new Legislation($_REQUEST['parent_id']);
                    $legislation->setParent_id   ($parent->getId());
                    $legislation->setCommittee_id($parent->getCommittee_id());
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }

            if (!empty($_REQUEST['type_id'])) {
                try { $legislation->setType_id($_REQUEST['type_id']); }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }
        }

        if (isset($legislation) && $legislation->getCommittee_id()) {
            $_SESSION['return_url'] =    !empty($_REQUEST['return_url'])
                                    ? urldecode($_REQUEST['return_url'])
                                    : ($legislation->getId()
                                        ? View::generateUrl('legislation.view').'?legislation_id='.$legislation->getId()
                                        : View::generateUrl('legislation.index'));

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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
        }

        if (isset($legislation)) {
            $committee_id = $legislation->getCommittee_id();
            $year         = $legislation->getYear();
            $return_url   = View::generateUrl('legislation.index')."?committee_id=$committee_id;year=$year";

            $legislation->delete();

            header("Location: $return_url");
            exit();
        }

        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks[] = new Block('404.inc');
        return $this->template;
    }
}
