<?php
/**
 * @copyright 2014-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Appointers\List;

class View extends \Web\View
{
    public function __construct($appointers)
    {
        parent::__construct();

        $this->vars = [
            'appointers'  => $this->appointer_data($appointers),
            'actionLinks' => $this->actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/appointers/list.twig', $this->vars);
    }

    private function appointer_data($appointers): array
    {
        $out     = [];
        $canEdit = parent::isAllowed('appointers', 'update');
        foreach ($appointers as $a) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('appointers.update').'?appointer_id='.$a->getId(),
                    'label' => parent::_('appointer_edit'),
                    'class' => 'edit'
                ];
            }
            $out[] = [
                'name'         => $a->getName(),
                'actionLinks' => $links
            ];
        }
        return $out;
    }

    private function actionLinks(): array
    {
        if (parent::isAllowed('appointers', 'update')) {
            return [[
                'url'   => parent::generateUri('appointers.update'),
                'label' => parent::_('appointer_add'),
                'class' => 'add'
            ]];
        }
        return [];
    }
}
