<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Find;

class View extends \Web\View
{
    public function __construct($people, int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'people'      => $people,
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'firstname'   => !empty($_GET['firstname']) ? parent::escape($_GET['firstname']) : '',
            'lastname'    => !empty($_GET['lastname' ]) ? parent::escape($_GET['lastname' ]) : '',
            'email'       => !empty($_GET['email'    ]) ? parent::escape($_GET['email'    ]) : '',
            'callback'    => !empty($_REQUEST['callback']),
            'return_url'  => !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : null
        ];
    }

    public function render(): string
    {
        $template = $this->vars['callback']
                    ? 'people/chooser'
                    : ($this->outputFormat == 'html' ? 'people/findForm' : 'people/list');
        return $this->twig->render("{$this->outputFormat}/$template.twig", $this->vars);
    }
}
