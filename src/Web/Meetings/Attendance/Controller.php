<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Attendance;

use Application\Models\Meeting;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_REQUEST['meeting_id'])) {
            try { $meeting = new Meeting($_REQUEST['meeting_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (isset($_POST['members'])) {
            $notes = $_POST['attendanceNotes'] ?? null;
            $data  = [];
            foreach ($_POST['members'] as $member_id=>$status) {
                $data[] = [
                    'meeting_id' => (int)$meeting->getId(),
                    'member_id'  => (int)$member_id,
                    'status'     => $status
                ];
            }
            try {
                $meeting->saveAttendance($data, $notes);
                header('Location: '.\Web\View::generateUrl('meetings.view', ['meeting_id'=>$meeting->getId()]));
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if ($meeting) {
            return new View($meeting);
        }
        return new \Web\Views\NotFoundView();
    }
}
