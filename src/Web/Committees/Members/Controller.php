<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Members;

use Application\Models\Committee;
use Application\Models\SeatTable;
use Application\Models\OfficeTable;
use Web\Seats\List\Controller as SeatsController;
use Web\View;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        if (!empty($params['id'])) {
            try { $committee = new Committee($params['id']); }
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
                $members = self::member_data($results);
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

    private static function member_data($results): array
    {
        $data    = [];
        $canView = \Web\View::isAllowed('people', 'viewContactInfo');
        $fields  = ['email', 'address', 'city', 'state', 'zip'];
        $ot      = new OfficeTable();
        foreach ($results as $m) {
            $offices = [];
            $person  = $m->getPerson();
            $search  = ['person_id'    => $m->getPerson_id(),
                        'committee_id' => $m->getCommittee_id(),
                        'current'      => date('Y-m-d') ];
            foreach ($ot->find($search) as $o) { $offices[] = $o; }

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
                'offices'                => $offices
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
}
