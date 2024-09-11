<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Delete;

use Application\Models\Legislation\Legislation;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\Vew
    {
        if (!empty($_REQUEST['legislation_id'])) {
            try { $legislation = new Legislation($_REQUEST['legislation_id']); }
            catch (\Exception $e) { $_SESSION['errorMesssages'][] = $e->getMessage(); }
        }

        if (isset($legislation)) {
            $committee_id = $legislation->getCommittee_id();
            $year         = $legislation->getYear();
            $return_url   = \Web\View::generateUrl('legislation.index')."?committee_id=$committee_id;year=$year";

            $legislation->delete();

            header("Location: $return_url");
            exit();
        }

        return new \Web\Views\NotFoundView();
    }
}
