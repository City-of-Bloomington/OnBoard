<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Offices\Update;

use Application\Models\Office;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (empty($_REQUEST['office_id'])) {
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
        }
        else {
            try { $office = new Office($_REQUEST['office_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
                header('Location: '.\Web\View::generateUrl('committees.index'));
                exit();
            }
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
                $return_url = \Web\View::generateUrl('committees.members').'?committee_id='.$office->getCommittee_id();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($office);
    }
}