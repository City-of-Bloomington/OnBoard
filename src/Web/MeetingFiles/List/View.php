<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\List;

use Application\Models\Committee;
use Application\Models\MeetingFile;

class View extends \Web\View
{
    public function __construct(array $files,
                                array $search,
                                array $sort,
                                array $years,
                                int   $totalItemCount,
                                int   $currentPage,
                                int   $itemsPerPage,
                                ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'committee'    => $committee,
            'files'        => $this->createFileData($files),
            'sort'         => $sort,
            'year'         => $search['year'] ?? null,
            'years'        => $years,
            'type'         => $search['type'] ?? null,
            'types'        => MeetingFile::$types,
            'actionLinks'  => $this->createActionLinks(),
            'total'        => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/meetingFiles/list.twig', $this->vars);
    }

    private function createActionLinks(): array
    {
        $url = parent::current_url();
        $url->format = 'csv';
        return [['url' => $url, 'label' => 'CSV Export', 'class'=>'download']];
    }

    private static function createFileData(array $files): array
    {
        $filedata = [];
        $userCanEdit   = parent::isAllowed('meetingFiles', 'update');
        $userCanDelete = parent::isAllowed('meetingFiles', 'delete');
        foreach ($files as $f) {
            $d = [
                'id'          => $f->getId(),
                'type'        => $f->getType(),
                'filename'    => $f->getFilename(),
                'title'       => $f->getTitle(),
                'meeting_id'  => $f->getMeeting_id(),
                'meetingDate' => $f->getMeeting()->getStart(DATE_FORMAT)
            ];
            $filedata[] = $d;
        }
        return $filedata;
    }
}
