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
use Web\View;

class LegislationFilesController extends Controller
{
    public function update(): View
    {
        if (!empty($_REQUEST['legislationFile_id'])) {
            try { $file = new LegislationFile((int)$_REQUEST['legislationFile_id']); }
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
                                    ? View::generateUrl('legislation.view').'?legislation_id='.$file->getLegislation_id()
                                    : View::generateUrl('legislation.index'));


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
        return $this->template;
    }

    /**
     * Tries to directly stream the file to the browser
     *
     * When we send files, we have to bypass the buffered templating system.
     * The normal Templates are all buffered, and larger files use up all
     * allowed memory defined in php.ini
     */
    public function download(): View
    {
        if (!empty($_GET['legislationFile_id'])) {
            try {
                $id   = (int)$_GET['legislationFile_id'];
                if (!$id) { throw new \Exception('files/unknownFile'); }
                $file = new LegislationFile($id);
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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_GET['legislationFile_id'])) {
            try {
                $file = new LegislationFile((int)$_GET['legislationFile_id']);
                $legislation_id = $file->getLegislation_id();
                $file->delete();
                $return_url = View::generateUrl('legislation.view')."?legislation_id=$legislation_id";
                header("Location: $return_url");
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
        return $this->template;
    }
}
