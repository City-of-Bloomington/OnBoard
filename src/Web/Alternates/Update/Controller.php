<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Update;

use Application\Models\Alternate;
use Application\Models\AlternateTable;
use Application\Models\Term;
use Application\Models\Seat;
use Application\Models\Committee;


class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (!empty($_REQUEST['alternate_id'])) { $alternate = new Alternate($_REQUEST['alternate_id']); }
            else {
                if     (!empty($_REQUEST['term_id'     ])) { $o = new Term($_REQUEST['term_id']); }
                elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat($_REQUEST['seat_id']); }
                elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
                $alternate = $o->newAlternate();
            }
            if (!empty($_REQUEST['person_id'])) { $alternate->setPerson_id($_REQUEST['person_id']); }
            if (!empty($_REQUEST['startDate'])) { $alternate->setStartDate($_REQUEST['startDate'], 'Y-m-d'); }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        if (isset($alternate)) {
            if (!empty($_POST['committee_id'])) {
                try {
                    if (!empty($_POST['endDate'])) {
                        $alternate->setEndDate($_POST['endDate'], 'Y-m-d');
                    }
                    else { $alternate->setEndDate(null); }

                    AlternateTable::update($alternate);

                    $url = $alternate->getSeat_id()
                            ? \Web\View::generateUrl('seats.view').'?seat_id='.$alternate->getSeat_id()
                            : \Web\View::generateUrl('committees.members').'?committee_id='.$alternate->getCommittee_id();

                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            return new View($alternate);

            $this->template->setFilename('contextInfo');
            $committee = $alternate->getCommittee();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('alternates/updateForm.inc',  ['alternate' => $alternate]);
            $seat = $alternate->getSeat();
            if ($seat) {
                $this->template->blocks['contextInfo'][] = new Block('seats/summary.inc', ['seat' => $seat]);
                $this->template->blocks[] = new Block('alternates/list.inc', [
                    'alternates'     => $seat->getAlternates(),
                                                      'disableButtons' => true
                ]);
            }
        }

        return new \Web\Views\NotFoundView();
    }
}