<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Meetings\List;

use Application\Models\Committee;
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;
use Application\Models\MeetingTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $search    = [];
        $committee = self::checkForCommittee($params);
        if ($committee) { $search['committee_id'] = $committee->getId(); }

        $dates     = self::checkForDates();
        if ($dates) { $search = array_merge($search, $dates); }
        else        { $search = array_merge($search, self::currentDates()); }
        if (!isset($search['year']) || !isset($search['start']) || !isset($search['end'])) {
            return new \Web\Views\BadRequestView();
        }

        if (!empty($_GET['fileType'])) {
            if (in_array($_GET['fileType'], MeetingFile::$types)) { $search['fileType'] = $_GET['fileType']; }
        }

        $page     = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort     = (!empty($_GET['sort']) && $_GET['sort']=='asc') ? 'asc' : 'desc';

        $table = new MeetingTable();
        if ($this->outputFormat !='json') {
            $list  = $table->find($search, "start $sort", true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);

            $totalItemCount = $list->getTotalItemCount();
        }
        else {
            $list  = $table->find($search, "start $sort");
            $totalItemCount = count($list);
        }

        $meetings = [];
        foreach ($list as $m) {
            $date  = $m->getStart('Y-m-d');
            $time  = $m->getStart('H:i:s');

            $files = [];
            foreach ($m->getMeetingFiles() as $f) { $files[$f->getType()][] = $f->getData(); }

            $meetings[$date][$time][] = [
                'id'       => $m->getId(),
                'title'    => $m->getTitle(),
                'eventId'  => $m->getEventId(),
                'location' => $m->getLocation(),
                'start'    => $m->getStart('c'),
                'end'      => $m->getEnd  ('c'),
                'htmlLink' => $m->getHtmlLink(),
                'files'    => $files
            ];
        }

        switch ($this->outputFormat) {
            case 'json':
                return new \Web\Views\JSONView($meetings);
            break;

            default:
                return new View($meetings,
                                $search,
                                $sort,
                                $totalItemCount,
                                $page,
                                parent::ITEMS_PER_PAGE,
                                $committee);
        }
    }

    private static function loadMeetingData(array $search, array $sort, int $page, ?Committee $committee): array
    {
        $table = new MeetingTable();
        if ($this->outputFormat == 'html') {
            $list  = $table->find($search);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);

            $totalItemCount = $list->getTotalItemCount();
        }
        else {
            $list  = $table->find($search);
            $totalItemCount = count($list);
        }
        $list  = $table->find($search);
        foreach ($list as $m) {
            $date  = $m->getStart('Y-m-d');
            $time  = $m->getStart('H:i:s');

            $files = [];
            foreach ($m->getMeetingFiles() as $f) { $files[$f->getType()][] = $f->getData(); }

            $meetings[$date][$time] = [
                'id'       => $m->getId(),
                'eventId'  => $m->getEventId(),
                'location' => $m->getLocation(),
                'start'    => $m->getStart('c'),
                'end'      => $m->getEnd  ('c'),
                'htmlLink' => $m->getHtmlLink(),
                'files'    => $files
            ];
        }
        if ($sort == 'asc') {  ksort($meetings); }
        else                { krsort($meetings); }
        return $meetings;
    }

    private static function checkForCommittee(array $params): ?Committee
    {
        if (!empty($_GET['committee_id'])) {
            try { return new Committee($_GET['committee_id']); }
            catch (\Exception $e) {  }
        }

        if (!empty($_REQUEST['committee_id'])) {
            try { return new Committee($_REQUEST['committee_id']); }
            catch (\Exception $e) {  }
        }

        return null;
    }

    private static function checkForDates(): array
    {
        if (!empty($_GET['start'])) {
            try {
                $start = new \DateTime($_GET['start']);
                if (!empty($_GET['end'])) { $end = new \DateTime($_GET['end']); }

                if (!isset($end)) {
                    $end = clone $start;
                    $end->add(new \DateInterval('P1Y'));
                }
                $year = (int)$start->format('Y');
                return [
                    'start' => $start,
                    'end'   => $end,
                    'year'  => $year
                ];
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = new \Exception('invalidDate');
            }
        }
        return [];
    }

    private static function currentDates(): array
    {
        $year  = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $start = new \DateTime("$year-01-01 00:00:00");
        $end   = new \DateTime("$year-12-31 23:59:59");
        return [
            'start' => $start,
            'end'   => $end,
            'year'  => $year
        ];
    }

    private static function years(Committee $c)
    {
        $table = new MeetingTable();
        return array_keys($table->years(['committee_id'=>$c->getId()]));
    }
}
