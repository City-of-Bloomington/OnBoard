<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Alternates\Delete;

use Application\Models\Alternate;
use Application\Models\AlternateTable;

use Web\View;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): View
    {
        try {
            if (!empty($_REQUEST['alternate_id'])) {
                $a  = new Alternate($_REQUEST['alternate_id']);

                $url = $a->getSeat_id()
                        ? View::generateUrl('seats.view',         ['id'=>$a->getSeat_id()])
                        : View::generateUrl('committees.members', ['id'=>$a->getCommittee_id()]);

                AlternateTable::delete($a);
                header("Location: $url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }

        header('Location: '.View::generateUrl('committees.index'));
        exit();
    }
}
