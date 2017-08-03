<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Application\Models\Legislation\Legislation;

$userCanEditActions = Person::isAllowed('legislationActions', 'update');
$helper = $this->template->getHelper('buttonLink');

echo '<table>';
foreach (Legislation::actionTypes() as $t) {
    $name   = parent::escape($t->getName());
    $action = $this->legislation->getAction($t);
    $button = '';

    if ($action) {
        $outcome    = parent::escape($action->getOutcome());
        $actionDate = $action->getActionDate(DATE_FORMAT);

        if ($userCanEditActions) {
            $button = $helper->buttonLink(
                BASE_URI.'/legislationActions/update?id='.$action->getId(),
                $this->_('legislationAction_edit'),
                'edit'
            );
        }
    }
    else {
        $outcome    = '';
        $actionDate = '';
        if ($userCanEditActions) {
            $params = http_build_query([
                'legislation_id' => $this->legislation->getId(),
                'type_id'        => $t->getId()
            ]);

            $button = $helper->buttonLink(
                BASE_URI."/legislationActions/update?$params",
                $this->_('legislationAction_add'),
                'add'
            );
        }
    }

    echo "
    <tr><th>$name</th>
        <td>$outcome $actionDate$button</td>
    </tr>
    ";
}
echo '</table>';
