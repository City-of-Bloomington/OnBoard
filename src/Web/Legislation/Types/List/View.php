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

        $links = [];
        if (parent::isAllowed('legislationTypes', 'update')) {
            $links['add'] = [
                'url'   => parent::generateUri('legislationTypes.update'),
                'label' => parent::_('legislationType_add')
            ];
        }

        $this->vars = [
            'types'       => $types,
            'actionLinks' => $links
        ];
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/legislation/types/list.twig', $this->vars);
    }
}
