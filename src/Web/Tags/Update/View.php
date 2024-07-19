<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Tags\Update;

use Application\Models\Tag;

class View extends \Web\View
{
    public function __construct(Tag $tag)
    {
        parent::__construct();
        $this->vars['tag'] = $tag;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/tags/update.twig", $this->vars);
    }
}
