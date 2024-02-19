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
    public function __construct(Person $person)
    {
        parent::__construct();

        $this->vars = [
            'person_id' => $person->getId(),
            'firstname' => $person->getFirstname(),
            'lastname'  => $person->getLastname(),
            'email'     => $person->getEmail(),
            'phone'     => $person->getPhone(),
            'website'   => $person->getWebsite(),
            'gender'    => $person->getGender(),
            'race_id'   => $person->getRace_id(),
            'address'   => $person->getAddress(),
            'city'      => $person->getCity(),
            'state'     => $person->getState(),
            'zip'       => $person->getZip()
        ];
        // Preserve any extra parameters passed in
        $params = [];
        foreach ($_REQUEST as $key=>$value) {
            if (!in_array($key, array_keys($this->vars))) { $params[$key] = $value; }
        }
        $this->vars['additional_params'] = $params;
        $this->vars['callback'         ] = isset($_REQUEST['callback']);

        $table = new RaceTable();
        $this->vars['races' ] = $table->find();
        $this->vars['states'] = Person::$STATES;

        if (!empty($_REQUEST['return_url'])) { $return_url = $_REQUEST['return_url']; }
        elseif ($person->getId())            { $return_url = $person->getUrl(); }
        else                                 { $return_url = parent::generateUrl('people.index'); }
        $this->vars['return_url'] = $return_url;
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/people/updateForm.twig", $this->vars);
    }
}
