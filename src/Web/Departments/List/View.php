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
        $this->vars['departments'] = $departments;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/departments/list.twig", $this->vars);
    }
}