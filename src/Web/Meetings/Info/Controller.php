<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Info;

use Application\Models\Meeting;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['meeting_id'])) {
            try { $meeting = new Meeting($_GET['meeting_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if ($meeting) {
            return new View($meeting);
        }
        return new \Web\Views\NotFoundView();
    }
}
