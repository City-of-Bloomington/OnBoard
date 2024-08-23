<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Terms\Update;

use Application\Models\Seat;
use Application\Models\Term;

class View extends \Web\View
{
    public function __construct(Seat $seat, Term $term)
    {
        parent::__construct();

        $this->vars = [
            'seat'      => $seat,
            'committee' => $seat->getCommittee(),
            'term'      => $term,
            'termIntervals' => Seat::$termIntervals,
            'termModifiers' => Seat::$termModifiers
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/terms/updateForm.twig', $this->vars);
    }
}
