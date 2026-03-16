<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Addresses\Update;

use Application\Models\Address;
use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Address $address, string $return_url)
    {
        parent::__construct();

        $this->vars = [
            'address'     => $address,
            'return_url'  => $return_url,
            'states'      => self::states()
        ];
    }

    public function render(): string
    {
        return $this->twig->render("html/people/addresses/updateForm.twig", $this->vars);
    }

    /**
     * Returns an array of options in the format expected by the forms macros
     *
     * @see templates/html/macros/forms.twig
     */
    public static function states(): array
    {
        $o = [['value'=>'']];
        foreach (Address::$STATES as $abbr=>$name) { $o[] = ['value'=>$abbr, 'label'=>$name]; }
        return $o;
    }
}
