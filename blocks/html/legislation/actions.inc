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

if ($userCanEditActions) {
    foreach (Legislation::actionTypes() as $t) {
        $params = http_build_query([
            'legislation_id' => $this->legislation->getId(),
            'type_id'        => $t->getId()
        ]);

        echo $helper->buttonLink(
            BASE_URI."/legislationActions/update?$params",
            sprintf($this->_('add_something', 'messages'), parent::escape($t->getName())),
            'add'
        );
    }
}

echo '<table>';
foreach (Legislation::actionTypes() as $t) {
    $name    = parent::escape($t->getName());
    $actions = $this->legislation->getActions(['type_id'=>$t->getId()]);
    $button  = '';

    foreach ($actions as $action) {
        $vote       = parent::escape($action->getVote());
        $outcome    = parent::escape($action->getOutcome());
        $actionDate = $action->getActionDate(DATE_FORMAT);

        if ($userCanEditActions) {
            $button = $helper->buttonLink(
                BASE_URI.'/legislationActions/update?id='.$action->getId(),
                $this->_('legislationAction_edit'),
                'edit'
            );
        }
        echo "
        <tr><th>$name</th>
            <td>$actionDate</td>
            <td>$outcome</td>
            <td>$vote</td>
            <td>$button</td>
        </tr>
        ";
    }

}
echo '</table>';
