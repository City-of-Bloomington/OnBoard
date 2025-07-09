<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Update;

use Application\Models\Person;
use Application\Models\RaceTable;

class View extends \Web\View
{
    public function __construct(Person $person, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'person'     => $person,
            'races'      => self::races(),
            'states'     => self::states(),
            'yesno'      => self::yesno(),
            'genders'    => self::genders(),
            'callback'   => isset($_REQUEST['callback']),
            'return_url' => $return_url
        ];

        // Preserve any extra parameters passed in
        $params = [];
        foreach ($_REQUEST as $key=>$value) {
            if (!in_array($key, array_keys($this->vars))) { $params[$key] = $value; }
        }
        $this->vars['additional_params'] = $params;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/people/updateForm.twig", $this->vars);
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    private static function states(): array
    {
        $o = [['value'=>'']];
        foreach (Person::$STATES as $s) { $o[] = ['value'=>$s]; }
        return $o;
    }

    private static function yesno(): array
    {
        return [
            ['value'=>1, 'label'=>parent::_('yes')],
            ['value'=>0, 'label'=>parent::_('no' )],
        ];
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function races(): array
    {
        $o = [['value'=>'']];
        $t = new RaceTable();
        foreach ($t->find() as $r) { $o[] = ['value'=>$r->getId(), 'label'=>$r->getName()]; }
        return $o;
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function genders(): array
    {
        return [
            ['value' => 'male'  ],
            ['value' => 'female']
        ];
    }
}
