<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Site;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class SiteController extends Controller
{
    public function index()
    {
        $this->template->blocks[] = new Block('site/content.inc');
    }

    public function updateContent()
    {
        if (!empty(     $_REQUEST['label'])
            && in_array($_REQUEST['label'], Site::$labels)) {

            if (isset($_POST['label'])) {
                try {
                    Site::saveContent($_POST);
                    header('Location: '.BASE_URL.'/site');
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = new Block('site/contentForm.inc', [
                'label'   => $_REQUEST['label'],
                'content' => Site::getContent($_REQUEST['label'])
            ]);
        }
        else {
            $_SESSION['errorMessages'][] = new \Exception('site/unknownLabel');
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}