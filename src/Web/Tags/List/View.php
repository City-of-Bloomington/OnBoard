<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\List;

class View extends \Web\View
{
    public function __construct($tags)
    {
        parent::__construct();
        $this->vars['tags'] = $tags;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/tags/list.twig", $this->vars);
    }
}
