<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Departments\List;

class View extends \Web\View
{
    public function __construct($departments)
    {
        parent::__construct();
        $this->vars = [
            'departments' => $this->department_data($departments),
            'actionLinks' => $this->actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/departments/list.twig", $this->vars);
    }

    private function department_data($departments): array
    {
        $out = [];
        $canEdit = parent::isAllowed('departments', 'update');
        foreach ($departments as $d) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('departments.update', ['department_id' => $d->getId()]),
                    'label' => parent::_('department_edit'),
                    'class' => 'edit'
                ];
            }
            $out[] = [
                'id'   => $d->getId(),
                'name' => $d->getName(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }

    private function actionLinks(): array
    {
        if (parent::isAllowed('departments', 'add')) {
            return [[
                'url'   => parent::generateUri('departments.add'),
                'label' => parent::_('department_add'),
                'class' => 'add'
            ]];
        }
    }
}
