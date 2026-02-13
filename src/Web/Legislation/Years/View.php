<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Years;

use Application\Models\Committee;
use Application\Models\Legislation\TypesTable;

class View extends \Web\View
{
    public function __construct(array $years,
                                array $search,
                                ?Committee $committee=null)
    {
        parent::__construct();

        $this->vars = [
            'years'     => $years,
            'committee' => $committee,
            'type_id'   => $search['type_id'  ] ?? null,
            'types'     => self::types()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/years.twig', $this->vars);
    }

    private static function types(): array
    {
        $o = [['value'=>'', 'label'=>'']];
        $t = new TypesTable();
        $l = $t->find(['subtype'=>false]);
        foreach ($l['rows'] as $c) { $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
        return $o;
    }
}
