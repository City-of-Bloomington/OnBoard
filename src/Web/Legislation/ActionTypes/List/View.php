<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\ActionTypes\List;

class View extends \Web\View
{
    public function __construct($types)
    {
        parent::__construct();
        $this->vars = [
            'types'       => $types,
            'actionLinks' => $this->actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render("html/legislation/actionTypes/list.twig", $this->vars);
    }

    private function actionLinks(): array
    {
        if (parent::isAllowed('legislationActionTypes', 'update')) {
            return [[
                'url'   => parent::generateUri('legislationActionTypes.update'),
                'label' => $this->_('legislationActionType_add'),
                'class' => 'add'
            ]];
        }
    }
}
