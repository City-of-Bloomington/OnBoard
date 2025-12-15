<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Update;

use Application\Models\MeetingTable;
use Application\Models\MeetingFile;
use Application\Models\Committee;
use Application\Models\File;

class View extends \Web\View
{
    public function __construct(MeetingFile $file)
    {
        parent::__construct();

        $meeting   = $file->getMeeting();
        $committee = $meeting->getCommittee();

        list($maxSize, $maxBytes) = File::maxUpload();

        $year = null;
        if (!empty($_REQUEST['year'])) { $year = (int)$_REQUEST['year']; }
        if (!$year) { $year = (int)$meeting->getStart('Y'); }
        if (!$year) { $year = (int)date('Y'); }


        $this->vars = [
             'meetingFile' => $file,
             'committee'   => $committee,
             'types'       => self::typeOptions(),
             'year'        => $year,
             'meetings'    => self::meetingOptions($committee, $year),
             'accept'      => self::mime_types(),
             'maxBytes'    => $maxBytes,
             'maxSize'     => $maxSize
         ];
    }

    public function render(): string
    {
        return $this->twig->render('html/meetingFiles/updateForm.twig', $this->vars);
    }

    private static function mime_types(): string
    {
        $accept = [];
        foreach (MeetingFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
        return implode(',', $accept);
    }

    private static function typeOptions(): array
    {
        $options = [];
        foreach (MeetingFile::$types as $t) { $options[] = ['value'=>$t]; }
        return $options;
    }

    private static function meetingOptions(Committee $committee, int $year): array
    {
        $start = new \DateTime("$year-01-01");
        $end   = new \DateTime("$year-01-01");
        $end->add(new \DateInterval('P1Y'));

        $table = new MeetingTable();
        $list  = $table->find([
            'committee_id' => $committee->getId(),
            'start'        => $start,
            'end'          => $end
        ]);

        $options = [['value'=>'', 'label'=>'']];
        foreach ($list['rows'] as $m) {
            $options[] = ['value'=>$m->getId(), 'label'=>$m->getStart('F j Y g:i a')];
        }
        return $options;
    }
}
