<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Application\Models\Member;
use Application\Models\SeatTable;
use Application\Models\OfficeTable;
use Web\Seats\List\Controller as SeatsController;
use Web\View;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($_REQUEST['committee_id'])) {
            try { $committee = new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($committee)) {

            if ($committee->getType() === 'seated') {
                $data      = SeatTable::currentData(['committee_id'=>$committee->getId()]);
                $seat_data = SeatsController::filter_viewable($data['results']);

                switch ($this->outputFormat) {
                    case 'csv':
                        return new \Web\Views\CSVView($committee->getName(), $seat_data);
                    break;

                    case 'json':
                        return new \Web\Views\JSONView($seat_data);
                    break;

                    default:
                        return new SeatedView($committee, $seat_data);
                }
            }
            else {
                $search = ['current' => true];
                if (isset($_GET['current']) && !$_GET['current']) {
                    $search['current'] = false;
                }

                $results = $committee->getMembers($search);
                $members = $this->member_data($results);
                switch ($this->outputFormat) {
                    case 'csv':
                        return new \Web\Views\CSVView($committee->getName(), $members);
                    break;

                    case 'json':
                        return new \Web\Views\JSONView($members);
                    break;

                    default:
                        return new OpenView($committee, $members, $search['current']);
                }
            }
        }
        return new \Web\Views\NotFoundView();
    }

    private function member_data($results): array
    {
        $data    = [];
        $canView = \Web\View::isAllowed('people', 'viewContactInfo');
        $fields  = ['email', 'address', 'city', 'state', 'zip'];
        foreach ($results as $m) {
            $person  = $m->getPerson();

            $row    = [
                'committee_id'           => $m->getCommittee_id(),
                'committee_name'         => $m->getCommittee()->getName(),
                'member_id'              => $m->getId(),
                'member_person_id'       => $m->getPerson_id(),
                'member_firstname'       => $person->getFirstname(),
                'member_lastname'        => $person->getLastname(),
                'member_website'         => $person->getWebsite(),
                'member_startDate'       => $m->getStartDate(),
                'member_endDate'         => $m->getEndDate(),
                'offices'                => $this->offices($m)
            ];
            if ($canView) {
                $row['member_email'  ] = $person->getEmail();
                $row['member_address'] = $person->getAddress();
                $row['member_city'   ] = $person->getCity();
                $row['member_state'  ] = $person->getState();
                $row['member_zip'    ] = $person->getZip();
            }
            $data[] = $row;
        }
        return $data;
    }

    private function offices(Member $m)
    {
        $offices = [];
        $table   = new OfficeTable();
        $search  = ['person_id'    => $m->getPerson_id(),
                    'committee_id' => $m->getCommittee_id(),
                    'current'      => date('Y-m-d') ];
        foreach ($table->find($search) as $o) { $offices[] = $o; }
        return $this->outputFormat == 'html'
               ? $offices
               : self::serializeOffices($offices);

    }

    /**
     * Formats an array of Offices to match SeatTable::currentData()
     *
     * SeatTable::currentData must return a single row per seat.
     * Because there can be many offices for a single membership,
     * we pack office ID and title into a string.
     *
     * @see Application\Models\SeatTable::$dataFields
     */
    private static function serializeOffices(array $offices): string
    {
        $out = [];
        foreach ($offices as $o) { $out[] = "{$o->getId()}|{$o->getTitle()}"; }
        return implode(',', $out);
    }
}
