<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Add;

use Application\Models\Committee;
use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\LegislationTable;
use Application\Models\CommitteeTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            $committee   = new Committee($params['committee_id']);
            $legislation = new Legislation();
            $legislation->setCommittee($committee);
        }
        catch (\Exception $e) { return new \Web\Views\NotFoundView(); }

        if (!empty($_REQUEST['parent_id'])) {
            try {
                $parent = new Legislation($_REQUEST['parent_id']);
                $legislation->setParent_id   ($parent->getId());
            }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (!empty($_REQUEST['type_id'])) {
            try { $legislation->setType_id($_REQUEST['type_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        $p = ['committee_id'=>$committee->getId()];
        if ($legislation->getId()) {
            $p['legislation_id'] = $legislation->getId();
            $return_url = \Web\View::generateUrl('legislation.view', $p);
        }
        elseif ($legislation->getParent_id()) {
            $p['legislation_id'] = $legislation->getParent_id();
            $return_url = \Web\View::generateUrl('legislation.view', $p);
        }
        else {
            $return_url = \Web\View::generateUrl('legislation.index', $p).'?year='.date('Y');
        }

        if (isset($_POST['number'])) {
            try {
                // Needed for the boolean toggle
                if (!isset($_POST['amendsCode'])) { $_POST['amendsCode'] = false; }

                $legislation->handleUpdate($_POST);
                $legislation->save();

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        return new \Web\Legislation\Update\View($legislation, $return_url);
    }
}
