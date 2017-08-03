<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->tags  An array of Tag objects
 */
use Application\Models\Person;

$userCanUpdate = Person::isAllowed('tags', 'update');
$helper = $this->template->getHelper('buttonLink');
?>
<section>
    <header>
        <h1><?= $this->_(['tag', 'tags', count($this->tags)]); ?></h1>
        <div class="tools">
        <?php
            if ($userCanUpdate) {
                echo $helper->buttonLink(
                    BASE_URI.'/tags/update',
                    $this->_('tag_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <ul>
    <?php
        foreach ($this->tags as $t) {
            $name = parent::escape($t->getName());

            $editButton = '';
            if ($userCanUpdate) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/tags/update?id='.$t->getId(),
                    $this->_('tag_edit'),
                    'edit'
                );
            }
            echo "<li>$name $editButton</li>";
        }
    ?>
    </ul>
</section>
