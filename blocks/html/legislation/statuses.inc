<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->statuses
 */
declare (strict_types=1);
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['legislationStatus', 'legislationStatuses', count($this->statuses)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislationStatuses', 'update');
            if ($userCanEdit) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/legislationStatuses/update',
                    $this->_('legislationStatus_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <ul>
    <?php
        foreach ($this->statuses as $s) {
            $editButton = '';
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/legislationStatuses/update?id='.$s->getId(),
                    $this->_('legislationStatus_edit'),
                    'edit'
                );
            }
            $name = parent::escape($s->getName());
            echo "<li>$editButton $name</li>";
        }
    ?>
    </ul>
</section>
