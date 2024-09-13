<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Types\List;

class View extends \Web\View
{
    public function __construct(array $types)
    {
        parent::__construct();


        $this->vars = [
            'types'       => $this->types_data($types),
            'actionLinks' => $this->actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/legislation/types/list.twig', $this->vars);
    }

    private function types_data($types): array
    {
        $out = [];
        $canEdit = parent::isAllowed('legislationTypes', 'update');
        foreach ($types as $t) {
            $links = [];
            if ($canEdit) {
                $links[] = [
                    'url'   => parent::generateUri('legislationTypes.update').'?id='.$t->getId(),
                    'label' => parent::_('legislationType_edit'),
                    'class' => 'edit'
                ];
            }

            $out[] = [
                'name'    => $t->getName(),
                'subtype' => $t->isSubtype(),
                'actionLinks' => $links
            ];
        }

        return $out;
    }

    private function actionLinks(): array
    {
        $links = [];
        if (parent::isAllowed('legislationTypes', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('legislationTypes.update'),
                'label' => parent::_('legislationType_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }
}
