<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Legislation\Update;

use Application\Models\Legislation\Legislation;
use Application\Models\Legislation\StatusesTable;
use Application\Models\Legislation\TypesTable;
use Application\Models\TagsTable;

class View extends \Web\View
{
    public function __construct(Legislation $legislation, string $return_url)
    {
        parent::__construct();

        $tags = $legislation->getTags();

        $this->vars = [
            'legislation' => $legislation,
            'committee'   => $legislation->getCommittee(),
            'return_url'  => $return_url,
            'title'       => $this->title($legislation),
            'types'       => self::types($legislation),
            'statuses'    => self::statuses($legislation),
            'tags'        => self::tags()
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/legislation/updateForm.twig', $this->vars);
    }

    private function title($legislation)
    {
        $type    = $legislation->getType();
        return $type
                ? ($legislation->getId()
                    ? sprintf($this->_('edit_something', 'messages'), $type->getName())
                    : sprintf($this->_( 'add_something', 'messages'), $type->getName()))
                : ($legislation->getId()
                    ? $this->_('legislation_edit')
                    : $this->_('legislation_add'));
    }

    private static function statuses(Legislation $l): array
    {
        $options = [['value'=>'', 'label'=>'']];
        $table   = new StatusesTable();
        $search  = $l->getId() ? null : ['active' => 1]; // New legislation should only use active statuses
        $list    = $table->find($search);
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }

    private static function types(Legislation $l): array
    {
        $options = [];
        $table   = new TypesTable();
        $list    = $table->find(['subtype'=>$l->getParent_id() ? true : false]);
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }

    private static function tags(): array
    {
        $options = [];
        $table   = new TagsTable();
        $list    = $table->find();
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>$t->getName()]; }
        return $options;
    }
}
