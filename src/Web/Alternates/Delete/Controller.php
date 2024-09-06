<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Delete;

use Application\Models\Alternate;
use Application\Models\AlternateTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (!empty($_REQUEST['alternate_id'])) {
                $alternate  = new Alternate($_REQUEST['alternate_id']);

                $return_url = $alternate->getSeat_id()
                            ? \Web\View::generateUrl('seats.view')."?seat_id={$alternate->getSeat_id()}"
                            : \Web\View::generateUrl('committees.members')."?committee_id={$alternate->getCommittee_id()}";

                AlternateTable::delete($alternate);
                header("Location: $return_url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        header('Location: '.\Web\View::generateUrl('committees.index'));
        exit();
    }
}
