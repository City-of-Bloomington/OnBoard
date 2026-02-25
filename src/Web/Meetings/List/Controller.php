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
    protected $valid_output_formats = ['html', 'json'];

    public function __invoke(array $params): \Web\View
    {
        $search    = [];
        $committee = self::checkForCommittee($params);
        if ($committee) { $search['committee_id'] = $committee->getId(); }

        try {
            $dates     = self::checkForDates();
            if ($dates) { $search = array_merge($search, $dates); }
            else        { $search['year'] = self::year(); }
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = 'invalidDate';
            return new \Web\Views\BadRequestView();
        }

        if (!empty($_GET['fileType'])) {
            if (in_array($_GET['fileType'], MeetingFile::$types)) { $search['fileType'] = $_GET['fileType']; }
        }

        $page     =  !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort     = (!empty($_GET['sort']) && $_GET['sort']=='asc') ? 'asc' : 'desc';

        $table = new MeetingTable();
        $list  = $this->outputFormat !='json'
               ? $table->find($search, "start $sort", parent::ITEMS_PER_PAGE, $page)
               : $table->find($search, "start $sort");

        $meetings = [];
        foreach ($list['rows'] as $m) {
            $date  = $m->getStart('Y-m-d');
            $time  = $m->getStart('H:i:s');

            $files = [];
            foreach ($m->getMeetingFiles() as $f) { $files[$f->getType()][] = $f->getData(); }

            $meetings[$date][$time][] = [
                'meeting'  => $m,
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
                                $list['total'],
                                $page,
                                parent::ITEMS_PER_PAGE,
                                $committee);
        }
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

    /**
     * @throws \Exception
     */
    private static function checkForDates(): array
    {
        if (!empty($_GET['start'])) {
            $start = new \DateTime($_GET['start']);
            if (!empty($_GET['end'])) { $end = new \DateTime($_GET['end']); }

            if (!isset($end)) {
                $end = clone $start;
                $end->add(new \DateInterval('P1Y'));
            }
            return [
                'start' => $start,
                'end'   => $end
            ];
        }
        return [];
    }

    /**
     * @throws \Exception
     */
    private static function year(): int
    {
        $maxY  = (int)date('Y') + 2;
        $year  = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        if ($year > $maxY) { throw new \Exception('invalidDate');}
        return $year;
    }

    private static function years(Committee $c)
    {
        $table = new MeetingTable();
        return array_keys($table->years(['committee_id'=>$c->getId()]));
    }
}
