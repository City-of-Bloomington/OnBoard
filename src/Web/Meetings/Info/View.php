<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Info;

use Application\Models\Meeting;
use Application\WarehouseService;
use Web\MeetingFiles\List\View as MeetingFileView;

class View extends \Web\View
{
    public function __construct(Meeting $m)
    {
        parent::__construct();

        $files = [];
        foreach ($m->getMeetingFiles() as $f) { $files[] = $f; }

        $this->vars = [
            'meeting'         => $m,
            'committee'       => $m->getCommittee(),
            'files'           => self::createFileData($files),
            'attendance'      => $m->hasAttendance() ? $m->getAttendance() : null,
            'attendanceNotes' => self::attendanceNotes($m),
            'actionLinks'     => self::actionLinks($m),
            'warehouse'       => self::warehouse_data($m)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/info.twig', $this->vars);
    }

    private static function actionLinks(Meeting $meeting): array
    {
        $links = [];
        if (parent::isAllowed('meetingFiles', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('meetingFiles.add').'?meeting_id='.$meeting->getId(),
                'label' => parent::_('meetingFile_add'),
                'class' => 'add'
            ];
        }
        if (parent::isAllowed('meetings', 'attendance')) {
            $links[] = [
                'url'   => parent::generateUri('meetings.attendance', ['meeting_id'=>$meeting->getId()]),
                'label' => parent::_('attendance')
            ];
        }
        return $links;
    }

    private static function createFileData(array $files): array
    {
        $filedata  = [];
        $canEdit   = parent::isAllowed('meetingFiles', 'update');
        $canDelete = parent::isAllowed('meetingFiles', 'delete');
        foreach ($files as $f) {
            $d = [
                'id'          => $f->getId(),
                'type'        => $f->getType(),
                'filename'    => $f->getFilename(),
                'title'       => $f->getTitle(),
                'meeting_id'  => $f->getMeeting_id(),
                'meetingDate' => $f->getMeeting()->getStart(DATE_FORMAT)
            ];
            if ($canEdit) {
                $d['actions'][] = [
                    'url'   => parent::generateUri('meetingFiles.update', ['id'=>$f->getId()]),
                    'label' => parent::_('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $d['actions'][] = [
                    'url'   => parent::generateUri('meetingFiles.delete', ['id'=>$f->getId()]),
                    'label' => parent::_('delete'),
                    'class' => 'delete'
                ];
            }
            $filedata[] = $d;
        }
        return $filedata;
    }

    private static function warehouse_data(Meeting $m): array
    {
        global $DATABASES;

        $info = [];
        if (array_key_exists('warehouse', $DATABASES)) {
            $staff = false;
            if (isset($_SESSION['USER'])) {
                $res   = WarehouseService::permitting_staff($_SESSION['USER']->getEmail());
                $staff = $res ? true : false;
            }

            $plans   = $staff
                     ? 'https://energov.bloomington.in.gov/energov_prod/manageplan/#/plan'
                     : 'https://energov.bloomington.in.gov/energov_prod/selfservice#/plan';
            $permits = $staff
                     ? 'https://energov.bloomington.in.gov/energov_prod/managepermit/#/permit'
                     : 'https://energov.bloomington.in.gov/energov_prod/selfservice#/permit';

            $info = WarehouseService::meeting_info((int)$m->getCommittee_id(), new \DateTime($m->getStart()));
            if (isset($info['permits'])) {
                foreach ($info['permits'] as $i=>$p) {
                    $info['permits'][$i]['url'] = $permits."/$p[permit_id]";
                }
            }
            if (isset($info['plans'])) {
                foreach ($info['plans'] as $i=>$p) {
                    $info['plans'][$i]['url'] = $plans."/$p[plan_id]";
                }
            }
        }
        return $info;
    }

    private static function attendanceNotes(Meeting $m): ?string
    {
        return parent::isAllowed('meetings', 'attendance')
                ? $m->getAttendanceNotes()
                : null;
    }
}
