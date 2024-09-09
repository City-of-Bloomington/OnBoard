<?php
/**
 * @copyright 2014-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Appointers\List;

class View extends \Web\View
{
    public function __construct($appointers)
    {
        parent::__construct();
        $this->vars['appointers'] = $appointers;
    }

    public function render(): string
    {
        return $this->twig->render('html/appointers/list.twig', $this->vars);
    }
}