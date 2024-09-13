<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare(strict_types=1);
namespace Web\Legislation\ActionTypes\Update;

use Application\Models\Legislation\ActionType;

class View extends \Web\View
{
    public function __construct(ActionType $type)
    {
        parent::__construct();

        $this->vars = [
            'type' => $type
        ];
    }

    public function render(): string
    {
        return $this->twig->render("html/legislation/actionTypes/updateForm.twig", $this->vars);
    }
}
