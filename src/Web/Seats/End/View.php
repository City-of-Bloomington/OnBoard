<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\End;

class View extends \Web\View
{
    public function __construct($seat)
    {
        parent::__construct();

        $this->vars = [
            'seat'      => $seat,
            'committee' => $seat->getCommittee()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/seats/endForm.twig', $this->vars);
    }
}
