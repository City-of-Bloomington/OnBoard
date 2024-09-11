<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Info;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\TypesTable;

use Web\Url;

class View extends \Web\View
{
    public function __construct(Legislation $legislation)
    {
        parent::__construct();

        $this->vars = [
            'legislation'        => $legislation,
            'committee'          => $legislation->getCommittee(),
            'actionLinks'        => $this->actionLinks($legislation),
            'childLinks'         => $this->childLinks($legislation),
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
            $params = [
                'legislation_id' => $legislation->getId(),
                'return_url'     => Url::current_url(BASE_HOST)
            ];
            $links[] = [
                'url'   => parent::generateUri('legislation.update').'?'.http_build_query($params, '', ';'),
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

    private function childLinks(Legislation $legislation): array
    {
        $links = [];
        if (!$legislation->getParent_id() && parent::isAllowed('legislation', 'update')) {
            $add   = parent::generateUri('legislation.update');
            $table = new TypesTable();
            $list  = $table->find(['subtype'=>true]);
            foreach ($list as $t) {
                $links[] = [
                    'url'   => $add."?type_id={$t->getId()};parent_id=".$legislation->getId(),
                    'label' => sprintf($this->_('add_something', 'messages'), $t->getName()),
                    'class' => 'add'
                ];
            }
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
