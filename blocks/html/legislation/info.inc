<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$committee  = parent::escape($this->legislation->getCommittee()->getName());
$type       = parent::escape($this->legislation->getType()     ->getName());
$amendsCode = $this->legislation->amendsCode() ? $this->_('yes') : $this->_('no');

$fields = ['id', 'number', 'title', 'synopsis', 'year'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislation->$get());
}
?>
<section>
    <header>
        <h1><?= "$year $type $number $amendsCode"; ?></h1>
        <div class="tools">
        <?php
            $h = $this->template->getHelper('buttonLink');

            if (Person::isAllowed('legislation', 'update')) {
                echo $h->buttonLink(
                    BASE_URI.'/legislation/update?id='.$id,
                    $this->_('legislation_edit'),
                    'edit'
                );
            }
        ?>
        </div>
        <h2><?= $title; ?></h2>
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
