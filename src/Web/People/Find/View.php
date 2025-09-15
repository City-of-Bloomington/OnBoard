<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Find;

class View extends \Web\View
{
    public function __construct(array $people, array $search, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'people'      => $people,
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'firstname'   => $search['firstname'] ?? '',
            'lastname'    => $search['lastname' ] ?? '',
            'email'       => $search['email'    ] ?? '',
            'callback'    => !empty($_REQUEST['callback']),
            'return_url'  => !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : null,
            'breadcrumbs' => self::breadcrumbs()
        ];
    }

    public function render(): string
    {
        $template = $this->vars['callback'] ? 'chooser' : 'find';
        return $this->twig->render("html/people/$template.twig", $this->vars);
    }

    private static function breadcrumbs(): array
    {
        return [
             parent::_(['person', 'people', 10]) => null
        ];
    }
}
