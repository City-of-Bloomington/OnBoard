<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Liaisons\Info;

use Application\Models\Committee;
use Application\Models\LiaisonTable;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();
        $committee_id = (int)$committee->getId();

        $this->vars = [
            'committee'     => $committee,
            'liaisons'     => $this->liaisonData ($committee_id),
            'actionLinks'  => $this->actionLinks ($committee_id)
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/committees/liaisons.twig', $this->vars);
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
                    'url'   => parent::generateUri('liaisons.update', ['id' => $row['liaison_id']]),
                    'label' => _('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDel) {
                $links[] = [
                    'url'   => parent::generateUri('liaisons.delete', ['id' => $row['liaison_id']]),
                    'label' => _('delete'),
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
        if (parent::isAllowed('liaisons', 'add')) {
            return [[
                'url'   => parent::generateUri('liaisons.add').'?committee_id='.$committee_id,
                'label' => _('liaison_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }

}
