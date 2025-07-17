<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
            'states'     => self::states(),
            'yesno'      => self::yesno(),
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
}
