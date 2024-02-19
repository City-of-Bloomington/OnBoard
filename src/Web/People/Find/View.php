<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Find;

class View extends \Web\View
{
    public function __construct($people,int $total, int $itemsPerPage, int $currentPage)
    {
        parent::__construct();

        $this->vars = [
            'people'      => $people,
            'total'       => $total,
            'itemsPerPage'=> $itemsPerPage,
            'currentPage' => $currentPage,
            'firstname'   => !empty($_GET['firstname']) ? parent::escape($_GET['firstname']) : '',
            'lastname'    => !empty($_GET['lastname' ]) ? parent::escape($_GET['lastname' ]) : '',
            'email'       => !empty($_GET['email'    ]) ? parent::escape($_GET['email'    ]) : ''
        ];
    }

    public function render(): string
    {
        $template = $this->outputFormat == 'html' ? 'people/findForm' : 'people/list';
        return $this->twig->render("{$this->outputFormat}/$template.twig", $this->vars);
    }
}
