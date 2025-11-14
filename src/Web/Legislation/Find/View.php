<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Find;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\Legislation\LegislationTable;
use Application\Models\Legislation\StatusesTable;
use Application\Models\Legislation\TypesTable;

class View extends \Web\View
{
    public function __construct(array $legislation,
                                array $search,
                                int   $totalItemCount,
                                int   $currentPage,
                                int   $itemsPerPage,
                                Committee $committee)
    {
        parent::__construct();

        $this->vars = [
            'legislation'  => $legislation,
            'committee'    => $committee,
            'search'       => $search,
            'committees'   => self::committees(),
            'years'        => self::years(),
            'statuses'     => self::statuses(),
            'types'        => self::types(),
            'total'        => $totalItemCount,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $currentPage,
            'actionLinks'  => $this->actionLinks($committee)
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/findForm.twig', $this->vars);
    }

    private static function committees(): array
    {
        $options = [['value'=>'', 'label'=>'']];
        $table   = new CommitteeTable();
        $list    = $table->find(['legislative'=>true]);
        $options = [];
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function years(): array
    {
        $out   = [];
        $table = new LegislationTable();
        $data  = $table->years();
        foreach (array_keys($data) as $y) { $out[] = ['value'=>$y]; }
        return $out;
    }

    private static function statuses(): array
    {
        $options = [['value'=>'', 'label'=>'']];
        $table = new StatusesTable();
        $list = $table->find();
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }

    private static function types(): array
    {
        $options = [['value'=>'', 'label'=>'']];
        $table   = new TypesTable();
        $list    = $table->find(['subtype'=>false]);
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }

    private function actionLinks(Committee $c): array
    {
        $links = [];
        if (parent::isAllowed('legislation', 'add')) {
            $links[] = [
                'url'   => parent::generateUri('legislation.add', ['committee_id'=>$c->getId()]),
                'label' => parent::_('legislation_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }
}
