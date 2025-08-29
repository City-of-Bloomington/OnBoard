<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Notifications\Definitions\Update;

use Application\Models\CommitteeTable;
use Application\Models\Notifications\Definition;

class View extends \Web\View
{
    public function __construct(Definition $d, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'definition' => $d,
            'committees' => self::committees(),
            'return_url' => $return_url
        ];
    }

    public function render(): string
    {
        return $this->twig->render('html/notifications/definitions/updateForm.twig', $this->vars);
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
        $l = $t->find();
        foreach ($l as $c) { $o[] = ['value'=>$c->getId(), 'label'=>$c->getName()]; }
        return $o;
    }
}
