<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Statuses\List;

class View extends \Web\View
{
    public function __construct($statuses)
    {
        parent::__construct();

        $this->vars = [
            'statuses'    => $this->status_data($statuses),
            'actionLinks' => $this->actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/statuses/list.twig', $this->vars);
    }

    private function status_data($statuses): array
    {
        $out     = [];
        $canEdit = parent::isAllowed('legislationStatuses', 'update');
        $canDel  = parent::isAllowed('legislationStatuses', 'delete');
        foreach ($statuses as $s) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('legislationStatuses.update', ['legislationStatus_id'=>$s->getId()]),
                    'label' => parent::_('legislationStatus_edit'),
                    'class' => 'edit'
                ];
            }
            if ($canDel) {
                $links[] = [
                    'url'   => parent::generateUri('legislationStatuses.delete', ['legislationStatus_id'=>$s->getId()]),
                    'label' => parent::_('legislationStatus_delete'),
                    'class' => 'delete'
                ];
            }

            $out[] = [
                'name'   => $s->getName(),
                'active' => $s->getActive() ? true : false,
                'actionLinks' => $links
            ];
        }
        return $out;
    }

    private function actionLinks(): array
    {
        if (parent::isAllowed('legislationStatuses', 'add')) {
            return [[
                'url'   => parent::generateUri('legislationStatuses.add'),
                'label' => parent::_('legislationStatus_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}
