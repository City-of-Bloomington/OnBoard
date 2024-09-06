<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Site\Update;

class View extends \Web\View
{
    public function __construct(string $label, string $content)
    {
        parent::__construct();

        $this->vars = [
            'label'   => $label,
            'content' => $content
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/site/contentForm.twig', $this->vars);
    }
}
