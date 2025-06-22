<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Offices\Add;

use Application\Models\Office;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $office = new Office();

        if (!empty($_REQUEST['committee_id']) && !empty($_REQUEST['person_id'])) {
            $office->setCommittee_id($_REQUEST['committee_id']);
            $office->setPerson_id($_REQUEST['person_id']);
        }
        else {
            $_SESSION['errorMessages'][] = 'offices/missingCommittee';
            header('Location: '.\Web\View::generateUrl('committees.index'));
            exit();
        }

        if (isset($_POST['title'])) {
            try {
                $office->setTitle($_POST['title']);
                $office->setStartDate($_POST['startDate'], 'Y-m-d');
                if (!empty($_POST['endDate'])) {
                    $office->setEndDate($_POST['endDate'], 'Y-m-d');
                }
                else {
                    $office->setEndDate(null);
                }

                $office->save();
                $url = \Web\View::generateUrl('committees.members', ['committee_id'=>$office->getCommittee_id()]);
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Offices\Update\View($office);
    }
}
