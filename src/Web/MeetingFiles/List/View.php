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
            'files'        => self::createFileData($files),
            'sort'         => $sort,
            'years'        => $years,
            'types'        => MeetingFile::$types,
            'actionLinks'  => self::createActionLinks(),
            'addLinks'     => self::createAddLinks($committee),
            'total'        => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/meetingFiles/list.twig', $this->vars);
    }

    private static function createActionLinks(): array
    {
        $url = parent::current_url();
        $url->format = 'csv';
        return [['url' => $url, 'label' => 'csv']];
    }

    private static function createAddLinks($committee): array
    {
        $addLinks = [];
        if ($committee && parent::isAllowed('meetingFiles', 'update')) {
            $id  = $committee->getId();
            $url = parent::generateUri('meetingFiles.update');
            foreach (MeetingFile::$types as $t) {
                $addLinks[] = [
                    'url'   => "$url?type=$t;committee_id=$id",
                    'label' => parent::_($t)
                ];
            }
        }
        return $addLinks;
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
                'meetingDate' => $f->getMeetingDate(DATE_FORMAT),
                'actions'     => []
            ];
            if ($userCanEdit) {
                $d['actions']['edit'] = [
                    'url'   => parent::generateUri('meetingFiles.update').'?meetingFile_id='.$f->getId(),
                    'label' => parent::_('meetingFile_edit')
                ];
            }
            if ($userCanDelete) {
                $d['actions']['delete'] = [
                    'url'   => parent::generateUri('meetingFiles.delete').'?meetingFile_id='.$f->getId(),
                    'label' => parent::_('meetingFile_delete')
                ];
            }
            $filedata[] = $d;
        }
        return $filedata;
    }
}