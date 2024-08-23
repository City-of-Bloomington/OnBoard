<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Add;

use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Seat $seat, array $appointers)
    {
        parent::__construct();

        $this->vars = [
            'seat'       => $seat,
            'committee'  => $seat->getCommittee(),
            'appointers' => $appointers,
            'types'         => Seat::$types,
            'termIntervals' => Seat::$termIntervals,
            'termModifiers' => Seat::$termModifiers
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/seats/addForm.twig', $this->vars);
    }
}
