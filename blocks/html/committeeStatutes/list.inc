<?php
/**
 * @copyright 2016-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->statutes
 * @param Committee         $this->committee (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section>
    <header>
        <h2><?= $this->_(['committeeStatute', 'committeeStatutes', count($this->statutes)]); ?></h2>
        <div class="tools">
        <?php
            $helper        = $this->template->getHelper('buttonLink');
            $userCanEdit   = Person::isAllowed('committeeStatutes', 'update');
            $userCanDelete = Person::isAllowed('committeeStatutes', 'delete');
            if ($this->committee && $userCanEdit) {
                echo $helper->buttonLink(
                    parent::generateUri('committeeStatutes.update').'?committee_id='.$this->committee->getId(),
                    $this->_('committeeStatute_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <?php
        $editButton    = '';
        $deleteButton  = '';
        foreach ($this->statutes as $s) {
            $citation = self::escape($s->getCitation());
            $url      = self::escape($s->getUrl());
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('committeeStatutes.update').'?committeeStatute_id='.$s->getId(),
                    $this->_('edit'),
                    'edit'
                );
                $editButton = "<td>$editButton</td>";
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    parent::generateUri('committeeStatutes.delete').'?committeeStatute_id='.$s->getId(),
                    $this->_('delete'),
                    'delete'
                );
                $deleteButton = "<td>$deleteButton</td>";
            }
            echo "
            <article>
                <header>
                    <h3><a href=\"$url\">$citation</a></h3>
                    <div class=\"tools\">$editButton $deleteButton</div>
                </header>
                <a href=\"$url\">$url</a>
            </article>
            ";
        }
    ?>
</section>
