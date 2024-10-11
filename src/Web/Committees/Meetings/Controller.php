<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Meetings;

use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!empty($_GET['start'])) {
            try {
                $start = new \DateTime($_GET['start']);
                if (!empty($_GET['end'])) { $end = new \DateTime($_GET['end']); }

                if (!isset($end)) {
                    $end = clone $start;
                    $end->add(new \DateInterval('P1Y'));
                }
                $year = (int)$start->format('Y');
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = new \Exception('invalidDate');
            }
        }
        else {
            $year = !empty($_GET['year'])
                  ?  (int) $_GET['year']
                  :  (int) date('Y');

            $start = new \DateTime("$year-01-01 00:00:00");
            $end   = new \DateTime("$year-12-31 23:59:59");
        }

        if (!isset($year) || !isset($start) || !isset($end)) {
            return new \Web\Views\BadRequestView();
        }

        if (isset($committee)) {
            $meetings = $committee->getMeetings($start, $end);

            switch ($this->outputFormat) {
                case 'json':
                    return new \Web\Views\JSONView($meetings);
                break;

                default:
                    return new View($meetings, $committee);
            }
        }

        return \Web\Views\NotFoundView();
    }
}
