<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Statutes\Info;

use Application\Models\Committee;

class View extends \Web\View
{
    public function __construct(Committee $committee, array $statutes)
    {
        parent::__construct();
        $committee_id = (int)$committee->getId();

        $this->vars = [
            'committee'     => $committee,
            'statutes'      => $statutes,
            'statuteData'   => $this->statuteData ($committee),
            'statuteLinks'  => $this->statuteLinks($committee_id)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/committees/statutes.twig', $this->vars);
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
                    'url'   => parent::generateUri('committeeStatutes.update', ['committeeStatute_id'=>$s->getId()]),
                    'label' => $this->_('edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDelete) {
                $links[] = [
                    'url'   => parent::generateUri('committeeStatutes.delete', ['committeeStatute_id'=>$s->getId()]),
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
                'url'   => parent::generateUri('committeeStatutes.add')."?committee_id=$committee_id",
                'label' => $this->_('add'),
                'class' => 'add'
            ]];
        }
        return [];
    }

}
