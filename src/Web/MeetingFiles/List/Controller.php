<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\MeetingFiles\List;

use Application\Models\Committee;
use Application\Models\MeetingFile;
use Application\Models\MeetingFilesTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $search = [];

        if (!empty($_GET['committee_id'])) {
            try {
                $committee = new Committee($_GET['committee_id']);
                $search['committee_id'] = $committee->getId();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort = [
            'field'     => 'meetingDate',
            'direction' => 'desc'
        ];
		if (!empty($_GET['sort'])) {
            $s = explode(' ', $_GET['sort']);
            $f = $s[0];
            $d = $s[1] ?? 'desc';
            if (in_array($f, MeetingFilesTable::$sortableFields)) {
                $sort['field']     = $f;
                $sort['direction'] = $d == 'asc' ? 'asc' : 'desc';
            }
		}

		if (!empty($_GET['type'])) {
            if (in_array($_GET['type'], MeetingFile::$types)) { $search['type'] = $_GET['type']; }
		}
		if (!empty($_GET['year'])) {
            $search['year'] = (int)$_GET['year'];
		}

        $table = new MeetingFilesTable();
        if ($this->outputFormat != 'csv') {
            $list  = $table->find($search, "$sort[field] $sort[direction]", true);
            $list->setCurrentPageNumber($page);
            $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);

            $totalItemCount = $list->getTotalItemCount();
        }
        else {
            $list  = $table->find($search, "$sort[field] $sort[direction]");
            $totalItemCount = count($list);
        }

        switch ($this->outputFormat) {
            case 'csv':
                $files = [];
                foreach ($list as $f) { $files[] = $f->getData(); }

                return new \Web\Views\CSVView('Meetings', $files);
            break;

            default:
                $files = [];
                foreach ($list as $f) { $files[] = $f; }

                return new View($files,
                                $search,
                                $sort,
                                $this->years($table, $search),
                                $totalItemCount,
                                $page,
                                parent::ITEMS_PER_PAGE,
                                $committee);
        }

    }

    private function years(MeetingFilesTable $table, array $search): array
    {
        if (isset($search['year'])) { unset($search['year']); }
        return array_keys($table->years($search));
    }
}
