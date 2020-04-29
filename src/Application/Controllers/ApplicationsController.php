<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Application;
use Application\Models\ApplicationTable;
use Application\Models\Committee;
use Application\Models\Seat;

use Web\Controller;
use Web\Block;
use Web\View;

class ApplicationsController extends Controller
{
    public function archive(): View
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
        return $this->template;
    }

    public function unarchive(): View
    {
        if (!empty($_REQUEST['application_id'])) {
            try { $application = new Application($_REQUEST['application_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($application)) {
            try { $application->unarchive(); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            header('Location: '.BASE_URI.'/committees/applications?committee_id='.$application->getCommittee_id());
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }

    public function report(): View
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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_REQUEST['application_id'])) {
            try { $application = new Application($_REQUEST['application_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($application)) {
            $committee_id = $application->getCommittee_id();
            $application->delete();

            header('Location: '.BASE_URL.'/committees/applications?committee_id='.$committee_id);
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
        return $this->template;
    }
}
