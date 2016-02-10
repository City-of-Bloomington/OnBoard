<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Application;
use Application\Models\ApplicationTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class ApplicationsController extends Controller
{
    public function index()
    {
    }

    public function archive()
    {
        if (!empty($_REQUEST['application_id'])) {
            try { $application = new Application($_REQUEST['application_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($application)) {
            try { $application->archive(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            header('Location: '.BASE_URI.'/committees/applications?committee_id='.$application->getCommittee_id());
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
