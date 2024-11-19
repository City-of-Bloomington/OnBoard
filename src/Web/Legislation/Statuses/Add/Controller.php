<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Statuses\Add;

use Application\Models\Legislation\Status;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $status = new Status();

        if (isset($_POST['name'])) {
            try {
                $status->handleUpdate($_POST);
                $status->save();
                header('Location: '.\Web\View::generateUrl('legislationStatuses.index'));
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new \Web\Legislation\Statuses\Update\View($status);
    }
}
