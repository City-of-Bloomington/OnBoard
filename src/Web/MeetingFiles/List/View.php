<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\List;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
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
            'year'         => $search['year'] ?? null,
            'type'         => $search['type'] ?? null,
            'years'        => self::years($years),
            'types'        => self::types(),
            'committees'   => self::committees(),
            'sorts'        => self::sorts(),
            'actionLinks'  => $this->createActionLinks(),
            'sort'         => implode(' ', $sort),
            'total'        => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetingFiles/list.twig', $this->vars);
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
                'meetingDate' => $f->getMeeting()->getStart(DATE_FORMAT),
                'committee'   => isset($committee) ? $committee->getName() : $f->getCommittee()->getName()
            ];
            $filedata[] = $d;
        }
        return $filedata;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function committees(): array
    {
        $o = [['value'=>'']];
        $t = new CommitteeTable();
        $l = $t->find();
        foreach ($l as $c) { $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function years(array $years): array
    {
        $o = [['value'=>'']];
        foreach ($years as $y) { $o[] = ['value'=>$y]; }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function types(): array
    {
        $o = [['value'=>'']];
        foreach (MeetingFile::$types as $t) { $o[] = ['value'=>$t]; }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function sorts(): array
    {
        return [
            ['value'=>'start asc',  'label'=>parent::translate('sort_date_asc' )],
            ['value'=>'start desc', 'label'=>parent::translate('sort_date_desc')]
        ];
    }
}
