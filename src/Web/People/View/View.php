<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\View;

use Application\Models\Person;

class View extends \Web\View
{
    public function __construct(Person $person, bool $disableButtons=false)
    {
        parent::__construct();

        $links = [];
        if (!$disableButtons) {
            if (parent::isAllowed('people', 'update')) {
                $links['edit'] = [
                    'url'   => parent::generateUri('people.update')."?person_id=".$person->getId(),
                    'label' => parent::_('person_edit')
                ];
            }
            if (parent::isAllowed('people', 'delete') && $person->isSafeToDelete()) {
                $links['delete'] = [
                    'url'   => parent::generateUri('people.delete')."?person_id=".$person->getId(),
                    'label' => parent::_('person_delete')
                ];
            }
        }

        $this->vars = [
            'person'      => $person,
            'actionLinks' => $links
        ];
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/people/info.twig", $this->vars);
    }
}