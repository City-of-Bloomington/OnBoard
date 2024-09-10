<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\Update;

use Application\Models\MeetingFile;
use Application\Models\Committee;
use Web\File;

class View extends \Web\View
{
    public function __construct(MeetingFile $file)
    {
        parent::__construct();

        list($maxSize, $maxBytes) = File::maxUpload();

        $year = null;
        if (!empty($_REQUEST['year'])) { $year = (int)$_REQUEST['year']; }
        if (!$year) { $year = (int)$file->getMeetingDate('Y'); }
        if (!$year) { $year = (int)date('Y'); }

       $this->vars = [
            'meetingFile' => $file,
            'committee'   => $file->getCommittee(),
            'types'       => self::typeOptions(),
            'year'        => $year,
            'events'      => self::eventOptions($file->getCommittee(), $year),
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

    private static function eventOptions(Committee $committee, int $year): array
    {
        $start = new \DateTime("$year-01-01");
        $end   = new \DateTime("$year-01-01");
        $end->add(new \DateInterval('P1Y'));

        $meetings = $committee->getMeetings($start, $end);
        $options  = [['value'=>'', 'label'=>'']];
        foreach ($meetings as $date=>$day) {
            foreach ($day as $event_id=>$m) {
                if (!empty($m['eventId'])) {
                    $start = new \DateTime($m['start']);
                    $options[] = ['value'=>$m['eventId'], 'label'=>$start->format('F j Y g:i a')];
                }
            }
        }
        return $options;
    }
}
