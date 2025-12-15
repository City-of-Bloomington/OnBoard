<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Seats\List;

use Application\Models\AppointerTable;
use Application\Models\CommitteeTable;

class View extends \Web\View
{
    public function __construct(array $data, array $search)
    {
        parent::__construct();

        $this->vars = [
            'data'         => $data,
            'committee_id' => $search['committee_id'] ?? null,
            'appointer_id' => $search['appointer_id'] ?? null,
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
            $p = http_build_query($p);

            return [['url' => parent::generateUri('seats.index')."?$p", 'label' => 'CSV Export', 'class' => 'download']];
        }
        return [];
    }
}
