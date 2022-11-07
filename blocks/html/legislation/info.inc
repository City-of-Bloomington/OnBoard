<?php
/**
 * @copyright 2017-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Web\Block;
use Michelf\Markdown;

$committee  = parent::escape($this->legislation->getCommittee()->getName());
$type       = parent::escape($this->legislation->getType()     ->getName());
$amendsCode = $this->legislation->amendsCode() ? $this->_('yes') : $this->_('no');
$status     = $this->legislation->getStatus_id()
            ? '('.parent::escape($this->legislation->getStatus()->getName()).')'
            : '';

$fields = ['id', 'number', 'title', 'synopsis', 'notes', 'year'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislation->$get());
}

$synopsis = $this->legislation->getSynopsis();
if ($synopsis) { $synopsis = Markdown::defaultTransform($synopsis); }
?>
<section>
    <header>
    <?php
        $h             = $this->template->getHelper('buttonLink');
        $userCanEdit   = Person::isAllowed('legislation', 'update');
        $userCanDelete = Person::isAllowed('legislation', 'delete');

        $tools = '';
        $links = [];
        if ($userCanEdit) {
            $links[] = $h->buttonLink(
                parent::generateUri('legislation.update')."?legislation_id=$id",
                $this->_('legislation_edit'),
                'edit'
            );
        }
        if ($userCanDelete) {
            $links[] = $h->buttonLink(
                parent::generateUri('legislation.delete')."?legislation_id=$id",
                $this->_('legislation_delete'),
                'delete'
            );
        }
        if ($links) {
            $tools = '<div class="tools">';
            foreach ($links as $l) { $tools.= $l; }
            $tools.= '</div>';
        }


        $parent = $this->legislation->getParent();
        if ($parent) {
            $block = new Block('legislation/partials/heading.inc', ['legislation'=>$parent]);
            echo $block->render('html', $this->template);

            echo "
            <h3>$year $type $number $status</h3>
            $tools
            <h4>$title</h4>
            ";
        }
        else {
            echo "
            <h1>$year $type $number $status</h1>
            $tools
            <h2>$title</h2>
            ";
        }

    ?>
    </header>
    <p><?= $synopsis; ?></p>
    <?php
        $tags = $this->legislation->getTags();
        if (count($tags)) {
            echo '<ul>';
            foreach ($tags as $t) {
                $name = parent::escape($t->getName());
                echo "<li>$name</li>";
            }
            echo '</ul>';
        }

        echo "<table><tr><th>{$this->_('amendsCode')}</th><td>$amendsCode</td></tr></table>";

        $this->_include('legislation/files.inc'  );
        $this->_include('legislation/actions.inc');

        $children = $this->legislation->getChildren();
        if ($children) {
            $block = new Block('legislation/list.inc', [
                'list'   => $children,
                'parent' => $this->legislation
            ]);
            echo $block->render('html', $this->template);
        }
    ?>
    <p><?= $notes; ?></p>
</section>
