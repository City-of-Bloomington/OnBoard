<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Attendance;

use Application\Models\Meeting;
use Web\MeetingFiles\List\View as MeetingFileView;

class View extends \Web\View
{
    public function __construct(Meeting $m)
    {
        parent::__construct();

        $this->vars = [
            'meeting'     => $m,
            'committee'   => $m->getCommittee(),
            'attendance'  => $m->getAttendance(),
            'attendance_options' => ['absent', 'in-person', 'remote']
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/attendanceForm.twig', $this->vars);
    }
}
