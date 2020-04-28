<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationFile;
use Application\Views\FileDownloadTemplate;

use Web\Controller;
use Web\Block;

class LegislationFilesController extends Controller
{
    public function index() { }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $file = new LegislationFile((int)$_REQUEST['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else { $file = new LegislationFile(); }

        if (!$file->getLegislation_id()) {
            if (!empty($_REQUEST['legislation_id'])) {
                try {
                    $l = new Legislation((int)$_REQUEST['legislation_id']);
                    $file->setLegislation($l);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
        }

        $_SESSION['return_url'] = !empty($_REQUEST['return_url'])
                                ? $_REQUEST['return_url']
                                : ($file->getLegislation_id()
                                    ? BASE_URL.'/legislation/view?id='.$file->getLegislation_id()
                                    : BASE_URL.'/legislation');


        if (isset($file) && $file->getLegislation_id()) {
            if (isset($_POST['legislation_id'])) {
                if (isset($_FILES['legislationFile']) && $_FILES['legislationFile']['error'] != UPLOAD_ERR_NO_FILE) {
                    try {
                        $file->setFile($_FILES['legislationFile']);
                        $file->save();

                        $return_url = $_SESSION['return_url'];
                        unset($_SESSION['return_url']);
                        header("Location: $return_url");
                        exit();
                    }
                    catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                }
            }

            $this->template->blocks[] = new Block('legislation/updateFileForm.inc', ['legislationFile'=>$file]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    /**
     * Tries to directly stream the file to the browser
     *
     * When we send files, we have to bypass the buffered templating system.
     * The normal Templates are all buffered, and larger files use up all
     * allowed memory defined in php.ini
     */
    public function download()
    {
        if (!empty($_GET['id'])) {
            try {
                $file = new LegislationFile((int)$_GET['id']);
                $file->sendToBrowser();
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_GET['id'])) {
            try {
                $file = new LegislationFile((int)$_GET['id']);
                $legislation_id = $file->getLegislation_id();
                $file->delete();
                header('Location: '.BASE_URL.'/legislation/view?id='.$legislation_id);
                exit();
            }
            catch (\Exception $e) {
                header('HTTP/1.1 404 Not Found', true, 404);
                $this->template->blocks[] = new Block('404.inc');
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
