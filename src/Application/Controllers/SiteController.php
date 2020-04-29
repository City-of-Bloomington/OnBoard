<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Site;
use Web\Controller;
use Web\Block;
use Web\View;

class SiteController extends Controller
{
    public function index(): View
    {
        $this->template->blocks[] = new Block('site/content.inc');
        return $this->template;
    }

    public function updateContent(): View
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
        return $this->template;
    }
}
