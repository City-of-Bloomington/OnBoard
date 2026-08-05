<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\List;

use Application\Models\AppointerTable;
use Application\Models\CommitteeTable;
use Application\Models\SeatTable;

class View extends \Web\View
{
    public function __construct(array $data, array $search)
    {
        parent::__construct();

        $this->vars = [
            'data'         => $data,
            'status'       => $search['status'      ] ?? null,
            'committee_id' => $search['committee_id'] ?? null,
            'appointer_id' => $search['appointer_id'] ?? null,
            'statuses'     => self::statuses(),
            'committees'   => self::committees(),
            'appointers'   => self::appointers(),
            'actionLinks'  => self::actionLinks($search)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/seats/list.twig', $this->vars);
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function statuses(): array
    {
        $o = [['value'=>'']];
        foreach (SeatTable::$valid_statuses as $s) {
            $o[] = ['value'=>$s, 'label'=>parent::_($s)];
        }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function committees(): array
    {
        $o = [['value'=>'']];
        $t = new CommitteeTable();
        $l = $t->find(['current'=>true]);
        foreach ($l['rows'] as $c) {
            $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()];
        }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function appointers(): array
    {
        $o = [['value'=>'']];
        $t = new AppointerTable();
        $l = $t->find();
        foreach ($l['rows'] as $a) {
            $o[] = ['value'=>$a->getId(), 'label'=>$a->getName()];
        }
        return $o;
    }

    private static function actionLinks(array $search): array
    {
        if (parent::isAllowed('people', 'viewContactInfo')) {
            $p = ['format' => 'csv'];
            if (!empty($search['committee_id'])) { $p['committee_id']=$search['committee_id']; }
            if (!empty($search['appointer_id'])) { $p['appointer_id']=$search['appointer_id']; }
            if (!empty($search['status'      ])) { $p['status'      ]=$search['status'      ]; }
            $p = http_build_query($p);

            return [['url' => parent::generateUri('seats.index')."?$p", 'label' => 'CSV Export', 'class' => 'download']];
        }
        return [];
    }
}
