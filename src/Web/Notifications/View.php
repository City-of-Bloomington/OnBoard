<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications;

class View extends \Web\View
{
    private $template;

    public function __construct(string $template, $model)
    {
        parent::__construct();

        $this->template = $this->twig->createTemplate($template);

        $this->vars = [
            'o' => $model
        ];
    }

    public function render(): string
    {
        return $this->template->render($this->vars);
    }
}
