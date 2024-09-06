<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\List;

class View extends \Web\View
{
    public function __construct(array $races)
    {
        parent::__construct();

        $this->vars = [
            'races' => $races
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/races/list.twig', $this->vars);
    }
}
