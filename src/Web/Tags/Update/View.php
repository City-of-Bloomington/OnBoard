<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\Update;

class View extends \Web\View
{
    private $tag;

    public function __construct($tag)
    {
        parent::__construct();
        $this->tag = $tag;
        $this->vars['tag'] = $this->tag;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/tags/update.twig", $this->vars);
    }
}
