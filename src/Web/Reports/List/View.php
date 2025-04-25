<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Reports\List;

use Application\Models\Committee;
use Application\Models\CommitteeTable;

class View extends \Web\View
{
    public function __construct($reports,
                                string $sort,
                                int    $total,
                                int    $itemsPerPage,
                                int    $currentPage,
                                ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'reports'      => self::report_data($reports),
            'committee'    => $committee,
            'committees'   => self::committees(),
            'sort'         => $sort,
            'sorts'        => self::sorts(),
            'total'        => $total,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'actionLinks'  => self::actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render("html/reports/list.twig", $this->vars);
    }

    private static function report_data($reports): array
    {
        $canEdit   = parent::isAllowed('reports', 'update');
        $canDelete = parent::isAllowed('reports', 'delete');

        $out = [];
        foreach ($reports as $r) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('reports.update', ['id'=>$r->getId()]),
                    'label' => parent::_('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('reports.delete', ['id'=>$r->getId()]),
                    'label' => parent::_('delete'),
                    'class' => 'delete'
                ];
            }
            $data  = $r->toArray();
            $data['actionLinks'] = $links;
            $out[] = $data;
        }
        return $out;
    }

    private static function actionLinks(?Committee $committee=null): array
    {
        if ($committee && parent::isAllowed('reports', 'add')) {
            return [[
                'url'   => parent::generateUri('reports.add').'?committee_id='.$committee->getId(),
                'label' => parent::_('report_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }

    private static function committees(): array
    {
        $o = [['value'=>'']];
        $t = new CommitteeTable();
        foreach ($t->find() as $c) { $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
        return $o;
    }

    private static function sorts(): array
    {
        return [
            ['value'=>'asc',  'label'=>parent::translate('sort_date_asc' )],
            ['value'=>'desc', 'label'=>parent::translate('sort_date_desc')]
        ];
    }
}
