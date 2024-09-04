<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Applications\Report;

use Application\Models\Application;
use Application\Models\Committee;
use Application\Models\Seat;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $seats = [];
                if (!empty(  $_REQUEST['seats'])) {
                    foreach ($_REQUEST['seats'] as $id) {
                        $id = (int)$id;
                        try { $seats[] = new Seat($id); }
                        catch (\Exception $e) { }
                    }
                }

                $applicants = [];
                if (!empty(  $_REQUEST['applications'])) {
                    foreach ($_REQUEST['applications'] as $id) {
                        $id = (int)$id;
                        try {
                            $a            = new Application($id);
                            $applicants[] = $a->getApplicant();
                        }
                        catch (\Exception $e) { }
                    }
                }
                return new View($applicants, $committee, $seats);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Views\NotFoundView();
    }
}
