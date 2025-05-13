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
            'appointers'   => self::appointers()
        ];

        if (parent::isAllowed('people', 'viewContactInfo')) {
            $this->vars['actionLinks'] = [['url' => parent::generateUri('seats.index').'?format=csv', 'label' => 'CSV Export', 'class' => 'download']];
        }
    }

    public function render(): string
    {
        return $this->twig->render($this->outputFormat.'/seats/list.twig', $this->vars);
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
        foreach ($l as $c) {
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
        foreach ($l as $a) {
            $o[] = ['value'=>$a->getId(), 'label'=>$a->getName()];
        }
        return $o;
    }
}
