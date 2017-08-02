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
        $table = new LegislationTable();
        $list  = $table->find($_GET);

        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
        }

        if (isset($committee)) {
            if ($this->template->outputFormat === 'html') {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
            }
        }

        $this->template->blocks[] = new Block('legislation/list.inc', ['legislation'=>$list]);
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

        $_SESSION['return_url'] = !empty($_REQUEST['return_url'])
                                ? $_REQUEST['return_url']
                                : $legislation->getId()
                                    ? BASE_URL.'/legislation/view?id='.$legislation->getId()
                                    : BASE_URL.'/legislation';

        if (isset($legislation)) {
            if (isset($_POST['number'])) {
                try {
                    $legislation->handleUpdate($_POST);
                    $legislation->save();

                    $return_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e; }
            }
            $this->template->blocks[] = new Block('legislation/updateForm.inc', ['legislation'=>$legislation]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
