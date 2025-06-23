<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Update;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (isset($legislation)) {
            if (!empty($_REQUEST['parent_id'])) {
                try {
                    $parent = new Legislation($_REQUEST['parent_id']);
                    $legislation->setParent($parent);
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            if (!empty($_REQUEST['type_id'])) {
                try { $legislation->setType_id($_REQUEST['type_id']); }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            $return_url = \Web\View::generateUrl('legislation.view', [
                'legislation_id' => $legislation->getId(),
                'committee_id'   => $legislation->getCommittee_id()
            ]);
            if (isset($_POST['number'])) {
                try {
                    // Needed for the new Bootstrap boolean toggle
                    if (!isset($_POST['amendsCode'])) { $_POST['amendsCode'] = false; }

                    $legislation->handleUpdate($_POST);
                    $legislation->save();

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
            }

            return new View($legislation, $return_url);
        }

        return new \Web\Views\NotFoundView();
    }
}
