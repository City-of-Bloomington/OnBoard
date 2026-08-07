<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Liaisons\List;

use Application\Models\CommitteeTable;
use Application\Models\Liaison;
use Application\Models\LiaisonTable;

class View extends \Web\View
{
    public function __construct(array $data, array $search)
    {
        parent::__construct();

        $this->vars = [
            'data'         => $data,
            'type'         => $search['type'        ] ?? null,
            'committee_id' => $search['committee_id'] ?? null,
            'status'       => $search['status'      ] ?? null,
            'committees'   => self::committees(),
            'statuses'     => self::statuses(),
            'types'        => self::types(),
            'actionLinks'  => self::actionLinks()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/liaisons/list.twig', $this->vars);
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
    private static function statuses(): array
    {
        $o = [['value'=>'']];
        foreach (LiaisonTable::$valid_statuses as $s) {
            $o[] = ['value'=>$s, 'label'=>parent::_($s)];
        }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function types(): array
    {
        $o = [['value'=>'']];
        foreach (LiaisonTable::$valid_types as $s) {
            $o[] = ['value'=>$s, 'label'=>parent::_($s)];
        }
        return $o;
    }

    private function actionLinks(): array
    {
        $out = [];
        if (parent::isAllowed('people', 'viewContactInfo')) {
            $uri = parent::generateUri('liaisons.index');
            $p   = parent::current_query_params();
            $out[] = [
                'url'   => $uri.'?'.http_build_query(array_merge($p, ['format'=>'csv'])),
                'label' => 'CSV',
                'class' => 'download'
            ];

            $out[] = [
                'url'   => $uri.'?'.http_build_query(array_merge($p, ['format'=>'email'])),
                'label' => 'Email List',
                'class' => 'mail'
            ];
        }

        return $out;
    }
}
