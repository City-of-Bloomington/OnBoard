<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Committees\Update;

use Application\Applications\Validators;
use Application\Models\Committee;
use Application\Models\DepartmentTable;

class View extends \Web\View
{
    public function __construct(Committee $committee)
    {
        parent::__construct();


        $this->vars = [
            'committee'         => $committee,
            'committee_types'   => self::committee_types(),
            'departments'       => self::departments(),
            'validators'        => self::validators()
        ];
    }

    public function render(): string
    {
        $form = $this->vars['committee']->getId() ? 'updateForm' : 'addForm';
        return $this->twig->render("html/committees/$form.twig", $this->vars);
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function committee_types(): array
    {
        $out = [];
        foreach (Committee::$types as $t) { $out[] = ['value'=>$t]; }
        return $out;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function departments(): array
    {
        $out = [];
        $t   = new DepartmentTable();
        $r   = $t->find();
        foreach ($r['rows'] as $d) { $out[] = ['value'=>$d->getId(), 'label'=>$d->getName()]; }
        return $out;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function validators(): array
    {
        $out = [];
        foreach (Validators::find() as $class) {
            $out[] = ['value'=>$class, 'label'=>$class::NAME];
        }
        return $out;
    }
}
