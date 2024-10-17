<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Races\Add;

use Application\Models\Race;

class View extends \Web\View
{
    public function __construct(Race $race)
    {
        parent::__construct();

        $this->vars = [
            'race' => $race
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/races/updateForm.twig', $this->vars);
    }
}
