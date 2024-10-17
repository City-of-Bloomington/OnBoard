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
            'files'       => MeetingFileView::createFileData($files),
            'actionLinks' => self::actionLinks($meeting)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetings/info.twig', $this->vars);
    }

    private static function actionLinks(Meeting $meeting): array
    {
        if (parent::isAllowed('meetingFiles', 'update')) {
            return [[
                'url'   => parent::generateUri('meetingFiles.update').'?meeting_id='.$meeting->getId(),
                'label' => parent::_('meetingFile_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}
