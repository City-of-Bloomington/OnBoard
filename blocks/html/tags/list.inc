<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->tags  An array of Tag objects
 */
use Application\Models\Person;

$userCanUpdate = Person::isAllowed('tags', 'update');
$helper        = $this->template->getHelper('buttonLink');
?>
<section>
    <header>
        <h1><?= $this->_(['tag', 'tags', count($this->tags)]); ?></h1>
        <div class="tools">
        <?php
            if ($userCanUpdate) {
                echo $helper->buttonLink(
                    parent::generateUri('tags.update'),
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
            $id         = $t->getId();
            $name       = parent::escape($t->getName());
            $editButton = '';
            if ($userCanUpdate) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('tags.update')."?id=$id",
                    $this->_('tag_edit'),
                    'edit'
                );
            }
            echo "<li>$name $editButton</li>";
        }
    ?>
    </ul>
</section>
