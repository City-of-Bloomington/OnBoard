<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\List;

class View extends \Web\View
{
    public function __construct($committees, bool $current)
    {
        parent::__construct();

        $links = [];
        if (parent::isAllowed('people', 'update')) {
            $links[] = [
                'url'   => parent::generateUri('committees.update'),
                'label' => parent::_('committee_add'),
                'class' => 'add'
            ];
        }

        $this->vars = [
            'committees'  => $committees,
            'current'     => $current,
            'actionLinks' => $links
        ];
    }

    public function render(): string
    {
        $template = $this->vars['current'] ? 'current' : 'past';
        return $this->twig->render("{$this->outputFormat}/committees/$template.twig", $this->vars);
    }
}
