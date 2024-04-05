<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\Action\Info;

class View extends \Web\View
{
    private $types;

    public function __construct($types)
    {
        parent::__construct();
        $this->types = $types;
        $this->vars['types'] = $this->types;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/legislation/action/info.twig", $this->vars);
    }
}

