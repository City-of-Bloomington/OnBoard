<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */

 // NOTE: Might need some work and fixes done
declare(strict_types=1);
namespace Web\Legislation\Action\Update;


class View extends \Web\View
{
    private $action;

    public function __construct($action)
    {
        parent::__construct();
        $this->action = $action;
        $this->vars['action'] = $this->action;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/legislation/action/update.twig", $this->vars);
    }
}
