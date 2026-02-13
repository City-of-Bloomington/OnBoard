<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\Update;

use Application\Models\AppointerTable;
use Application\Models\Seat;

class View extends \Web\View
{
    public function __construct(Seat $seat)
    {
        parent::__construct();

        $this->vars = [
            'seat'          => $seat,
            'committee'     => $seat->getCommittee(),
            'appointers'    => self::appointers(),
            'types'         => self::types(),
            'termIntervals' => self::termIntervals(),
            'termModifiers' => self::termModifiers()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/seats/updateForm.twig', $this->vars);
    }

    private static function appointers(): array
    {
        $out = [];
        $t   = new AppointerTable();
        $l   = $t->find();
        foreach ($l['rows'] as $a) { $out[] = ['value'=>$a->getId(), 'label'=>$a->getName()]; }
        return $out;
    }

    private static function types(): array
    {
        $o = [];
        foreach (Seat::$types as $t) { $o[] = ['value'=>$t]; }
        return $o;
    }

    private static function termIntervals(): array
    {
        $o = [];
        foreach (Seat::$termIntervals as $value=>$label) {
            $o[] = ['value'=>$value, 'label'=>$label];
        }
        return $o;
    }

    private static function termModifiers(): array
    {
        $o = [['value'=>'']];
        foreach (Seat::$termModifiers as $value=>$label) {
            $o[] = ['value'=>$value, 'label'=>$label];
        }
        return $o;
    }
}
