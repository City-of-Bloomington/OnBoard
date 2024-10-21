<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\Info;

use Application\Models\Meeting;
use Web\MeetingFiles\List\View as MeetingFileView;

class View extends \Web\View
{
    public function __construct(Meeting $meeting)
    {
        parent::__construct();

        $files = [];
        foreach ($meeting->getMeetingFiles() as $f) { $files[] = $f; }

        $this->vars = [
            'meeting'     => $meeting,
            'committee'   => $meeting->getCommittee(),
            'files'       => self::createFileData($files),
            'actionLinks' => self::actionLinks($meeting)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/info.twig', $this->vars);
    }

    private static function actionLinks(Meeting $meeting): array
    {
        if (parent::isAllowed('meetingFiles', 'add')) {
            return [[
                'url'   => parent::generateUri('meetingFiles.add').'?meeting_id='.$meeting->getId(),
                'label' => parent::_('meetingFile_add'),
                'class' => 'add'
            ]];
        }
        return [];
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
                    'label' => parent::_('meetingFile_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $d['actions'][] = [
                    'url'   => parent::generateUri('meetingFiles.delete', ['id'=>$f->getId()]),
                    'label' => parent::_('meetingFile_delete'),
                    'class' => 'delete'
                ];
            }
            $filedata[] = $d;
        }
        return $filedata;
    }
}
