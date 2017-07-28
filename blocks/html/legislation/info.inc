<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Application\Models\Legislation\Legislation;

$committee = parent::escape($this->legislation->getCommittee()->getName());
$type      = parent::escape($this->legislation->getType()     ->getName());
$number    = parent::escape($this->legislation->getNumber());
$title     = parent::escape($this->legislation->getTitle());
$synopsis  = parent::escape($this->legislation->getSynopsis());

?>
<section>
    <header>
        <h1><?= "$type $number"; ?></h1>
        <h2><?= $title; ?></h2>
        <div class="tools">
        <?php
            if (Person::isAllowed('legislation', 'update')) {
                $h = $this->template->getHelper('buttonLink');
                echo $h->buttonLink(
                    BASE_URI.'/legislation/update?id='.$this->legislation->getId(),
                    $this->_('legislation_edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>
    <p><?= $synopsis; ?></p>

    <table>
    <?php
        $userCanEditActions = Person::isAllowed('legislationActions', 'update');

        foreach (Legislation::actionTypes() as $t) {
            $name = parent::escape($t->getName());
            $action = $this->legislation->getAction($t);

            if ($action) {
                $outcome    = parent::escape($action->getOutcome());
                $actionDate = $action->getActionDate(DATE_FORMAT);

                if ($userCanEditActions) {
                    $button = $h->buttonLink(
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

                    $button = $h->buttonLink(
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
    ?>
    </table>
</section>
