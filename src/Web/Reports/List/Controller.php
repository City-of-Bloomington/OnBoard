<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Models\Committee;
use Application\Models\Reports\ReportsTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($_GET['committee_id'])) {
            try { $committee = new Committee($_GET['committee_id']); }
            catch (\Exception $e) {
                $_SESSION['errorMesssages'][] = $e->getMessage();
                unset($_GET['committee_id']);
            }
        }

        $table = new ReportsTable();

        switch ($this->outputFormat) {
            case 'json':
            break;

            default:
                $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
                $list  = $table->find($_GET, 'reportDate desc', true);
                $list->setCurrentPageNumber($page);
                $list->setItemCountPerPage(parent::ITEMS_PER_PAGE);

                return new View($list,
                                $list->getTotalItemCount(),
                                parent::ITEMS_PER_PAGE,
                                $page,
                                $committee ?? null);
        }
        if ($this->template->outputFormat == 'html') {

            if (isset($committee)) {
                $this->template->title = $committee->getName();
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
                $vars['committee'] = $committee;
            }
            $this->template->blocks[] = new Block('reports/list.inc',   $vars);
            $this->template->blocks[] = new Block('pageNavigation.inc', ['paginator' => $list]);
        }
        else {
            $this->template->blocks[] = new Block('reports/list.inc', [
                'list' => $table->find($_GET, 'reportDate desc')
            ]);
        }
        return $this->template;
    }
}
