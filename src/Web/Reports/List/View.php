<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct($reports, int $total, int $itemsPerPage, int $currentPage, ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'reports'      => $this->report_data($reports),
            'total'        => $total,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'committee'    => $committee,
            'actionLinks'  => $this->actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/reports/list.twig', $this->vars);
    }

    private function report_data($reports): array
    {
        $canEdit   = parent::isAllowed('reports', 'update');
        $canDelete = parent::isAllowed('reports', 'delete');

        $out = [];
        foreach ($reports as $r) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('reports.update').'?report_id='.$r->getId(),
                    'label' => $this->_('report_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('reports.delete').'?report_id='.$r->getId(),
                    'label' => $this->_('report_delete'),
                    'class' => 'delete'
                ];
            }
            $data  = $r->toArray();
            $data['actionLinks'] = $links;
            $out[] = $data;
        }
        return $out;
    }

    private function actionLinks(?Committee $committee=null): array
    {
        if ($committee && parent::isAllowed('reports', 'update')) {
            return [[
                'url'   => parent::generateUri('reports.update').'?committee_id='.$committee->getId(),
                'label' => $this->_('report_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}