<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Blossom\Classes\Block;
use Michelf\MarkdownExtra;

$committee  = parent::escape($this->legislation->getCommittee()->getName());
$type       = parent::escape($this->legislation->getType()     ->getName());
$amendsCode = $this->legislation->amendsCode() ? $this->_('yes') : $this->_('no');

$fields = ['id', 'number', 'title', 'synopsis', 'year'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislation->$get());
}

$markdown = new MarkdownExtra();
$synopsis = $markdown->transform($this->legislation->getSynopsis());
?>
<section>
    <header>
    <?php
        $h = $this->template->getHelper('buttonLink');

        $tools = '';
        if (Person::isAllowed('legislation', 'update')) {
            $tools = '<div class="tools">';
            $tools.= $h->buttonLink(
                BASE_URI.'/legislation/update?id='.$id,
                $this->_('legislation_edit'),
                'edit'
            );
            $tools.= '</div>';
        }

        $parent = $this->legislation->getParent();
        if ($parent) {
            $block = new Block('legislation/partials/heading.inc', ['legislation'=>$parent]);
            echo $block->render('html', $this->template);

            echo "
            <h3>$year $type $number</h3>
            $tools
            <h4>$title</h4>
            ";
        }
        else {
            echo "
            <h1>$year $type $number</h1>
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
</section>
