<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Info;

use Application\Models\Committee;
use Application\Models\LiaisonTable;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();
        $committee_id = (int)$committee->getId();

        $this->vars = [
            'committee'    => $committee,
            'liaisons'     => $this->liaisonData ($committee_id),
            'statutes'     => $this->statuteData ($committee),
            'statuteLinks' => $this->statuteLinks($committee_id),
            'actionLinks'  => $this->actionLinks ($committee_id),
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/committees/info.twig", $this->vars);
    }

    private function liaisonData(int $committee_id): array
    {
        $canEdit = parent::isAllowed('liaisons', 'update');
        $canDel  = parent::isAllowed('liaisons', 'delete');
        $res     = LiaisonTable::committeeLiaisonData(['committee_id'=>$committee_id]);
        $data    = [];
        foreach ($res['results'] as $row) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('liaisons.update')."?liaison_id=$row[liaison_id]",
                    'label' => _('liaison_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDel) {
                $links[] = [
                    'url'   => parent::generateUri('liaisons.delete')."?liaison_id=$row[liaison_id]",
                    'label' => _('liaison_delete'),
                    'class' => 'delete'
                ];
            }
            $row['actionLinks'] = $links;
            $data[] = $row;
        }
        return $data;
    }

    private function actionLinks(int $committee_id): array
    {
        $links = [];
        $param = "?committee_id=$committee_id";
        if (parent::isAllowed('committees', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('committees.update').$param,
                'label' => parent::_('edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('committees', 'end')) {
            $links[] = [
                'url'    => parent::generateUri('committees.end').$param,
                'label'  => parent::_('committee_end'),
                'class'  => 'delete'
            ];
        }
        return $links;
    }

    private function statuteData(Committee $committee): array
    {
        $canEdit   = parent::isAllowed('committeeStatutes', 'update');
        $canDelete = parent::isAllowed('committeeStatutes', 'delete');

        $out = [];
        foreach ($committee->getStatutes() as $s) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('committeeStatutes.update').'?committeeStatute_id='.$s->getId(),
                    'label' => $this->_('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('committeeStatutes.delete').'?committeeStatute_id='.$s->getId(),
                    'label' => $this->_('delete'),
                    'class' => 'delete'
                ];
            }
            $out[] = [
                'citation'    => $s->getCitation(),
                'url'         => $s->getUrl(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }

    private function statuteLinks(int $committee_id): array
    {
        if (parent::isAllowed('committeeStatutes', 'update')) {
            return [[
                'url'   => parent::generateUri('committeeStatutes.update')."?committee_id=$committee_id",
                'label' => $this->_('committeeStatute_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}
