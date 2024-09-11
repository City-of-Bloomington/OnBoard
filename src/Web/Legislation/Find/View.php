<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
                                ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'legislation'  => $legislation,
            'committee'    => $committee,
            'year'         => $search['year'     ] ?? null,
            'status_id'    => $search['status_id'] ?? null,
            'type_id'      => $search['type_id'  ] ?? null,
            'number'       => $search['number'   ] ?? null,
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

    private static function years(): array
    {
        $table = new LegislationTable();
        $data  = $table->years();
        return array_keys($data);
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

    private function actionLinks(?Committee $committee=null): array
    {
        $links = [];
        if ($committee && parent::isAllowed('legislation', 'update')) {
            $links[] = [
                'url' => parent::generateUri('legislation.update').'?committee_id='.$committee->getId(),
                'label' => parent::_('legislation_add'),
                'class' => 'add'
            ];
        }
        return $links;
    }
}
