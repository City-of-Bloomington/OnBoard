<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Application;
use Application\Models\ApplicationTable;
use Application\Models\Committee;
use Application\Models\Seat;
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

    public function report()
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($committee)) {
            $seats = [];
            if (!empty(  $_REQUEST['seats'])) {
                foreach ($_REQUEST['seats'] as $id) {
                    $id = (int)$id;
                    try { $seats[] = new Seat($id); }
                    catch (\Exception $e) { }
                }
            }

            $applications = [];
            if (!empty(  $_REQUEST['applications'])) {
                foreach ($_REQUEST['applications'] as $id) {
                    $id = (int)$id;
                    try { $applications[] = new Application($id); }
                    catch (\Exception $e) { }
                }
            }
            $this->template->blocks[] = new Block('applications/report.inc', [
                'applications' => $applications,
                'committee'    => $committee,
                'seats'        => $seats
            ]);
        }

    }
}
