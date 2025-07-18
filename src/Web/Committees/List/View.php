<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        if (parent::isAllowed('people', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('committees.add'),
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
        return $this->twig->render("html/committees/$template.twig", $this->vars);
    }
}
