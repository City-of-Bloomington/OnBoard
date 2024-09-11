<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Info;

use Application\Models\Legislation\Legislation;

class View extends \Web\View
{
    public function __construct(Legislation $legislation)
    {
        parent::__construct();

        $this->vars = [
            'legislation'        => $legislation,
            'committee'          => $legislation->getCommittee(),
            'actionLinks'        => $this->actionLinks($legislation),
            'legislationActions' => $this->action_data($legislation)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/info.twig', $this->vars);
    }

    private function actionLinks(Legislation $legislation): array
    {
        $links = [];
        if (parent::isAllowed('legislation', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('legislation.update').'?legislation_id='.$legislation->getId(),
                'label' => parent::_('legislation_edit'),
                'class' => 'edit'
            ];
        }
        if (parent::isAllowed('legislation', 'delete')) {
            $links[] = [
                'url'   => parent::generateUri('legislation.delete').'?legislation_id='.$legislation->getId(),
                'label' => parent::_('legislation_delete'),
                'class' => 'delete'
            ];
        }
        return $links;
    }

    private function action_data($legislation): array
    {
        $data    = [];
        $canEdit = parent::isAllowed('legislationActions', 'update');

        foreach (Legislation::actionTypes() as $t) {
            $type        = $t->getName();
            $data[$type] = [];
            $actions     = $legislation->getActions(['type_id'=>$t->getId()]);
            foreach ($actions as $action) {
                $links = [];
                if ($canEdit) {
                    $links[] = [
                        'url'   => parent::generateUri('legislationActions.update').'?legislationAction_id='.$action->getId(),
                        'label' => $this->_('legislationAction_edit'),
                        'class' => 'edit'
                    ];
                }
                $data[$type][] = [
                    'vote'    => $action->getVote(),
                    'outcome' => $action->getOutcome(),
                    'date'    => $action->getActionDate(),
                    'actionLinks' => $links
                ];
            }

        }
        return $data;
    }
}
