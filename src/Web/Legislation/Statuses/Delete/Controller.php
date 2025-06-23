<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Statuses\Delete;

use Application\Models\Legislation\Status;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['legislationStatus_id'])) {
            try { $status = new Status($_REQUEST['legislationStatus_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($status)) {
            $status->delete();
        }

        header('Location: '.\Web\View::generateUrl('legislationStatuses.index'));
        exit();
    }
}
