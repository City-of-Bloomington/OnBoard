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
            'termModifiers' => Seat::$termModifiers,
            'breadcrumbs'   => self::breadcrumbs($seat, $term)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/terms/updateForm.twig', $this->vars);
    }

    private static function breadcrumbs(Seat $s, Term $t)
    {
        $committee_id = $s->getCommittee_id();
        $committee    = $s->getCommittee()->getName();
        $seats        = parent::_(['seat', 'seats', 10]);
        $seat         = "{$s->getCode()} {$s->getName()}";
        $term         = $t->getId() ? parent::_('term_edit') : parent::_('term_add');
        return [
            $committee  => parent::generateUri('committees.info',  ['committee_id'=>$committee_id]),
            $seats      => parent::generateUri('committees.seats', ['committee_id'=>$committee_id]),
            $seat       => parent::generateUri('seats.view',       ['seat_id' => $s->getId()]),
            $term       => null
        ];
    }
}
