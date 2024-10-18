<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\Update;

use Application\Models\Committee;
use Application\Models\Liaison;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['liaison_id'])) {
            try {
                $liaison = new Liaison($_REQUEST['liaison_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }
        elseif (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $liaison   = new Liaison();
                $liaison->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($liaison)) {
            if (!empty($_REQUEST['person_id'])) {
                try {
                    $person = new Person($_REQUEST['person_id']);
                    $liaison->setPerson($person);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            if (isset($_POST['person_id'])) {
                try {
                    $liaison->handleUpdate($_POST);
                    $liaison->save();

                    $return_url = \Web\View::generateUrl('committees.info', ['id'=>$liaison->getCommittee_id()]);

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
            }

            return new View($liaison);

            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $liaison->getCommittee()]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $liaison->getCommittee()]);
            $this->template->blocks[] = new Block('liaisons/updateForm.inc',    ['liaison'   => $liaison]);
        }

        return new \Web\Views\NotFoundView();
    }
}
