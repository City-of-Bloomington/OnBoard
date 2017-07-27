<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->legislation An array of Legislation objects
 */
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['legislation', 'legislations', count($this->legislation)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislation', 'update');
            $h = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $h->buttonLink(
                    BASE_URI.'/legislation/update',
                    $this->_('legislation_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
        <thead>
            <tr><th><?= $this->_('legislationType'); ?></th>
                <th><?= $this->_('number'         ); ?></th>
                <th><?= $this->_('title'          ); ?></th>
                <th><?= $this->_('synopsis'       ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->legislation as $l) {
                $type      = parent::escape($l->getType()->getName());
                $number    = parent::escape($l->getNumber  ());
                $title     = parent::escape($l->getTitle   ());
                $synopsis  = parent::escape($l->getSynopsis());

                $uri = BASE_URI.'/legislation/view?id='.$l->getId();

                echo "
                <tr><td>$type</td>
                    <td><a href=\"$uri\">$number</a></td>
                    <td>$title</td>
                    <td>$synopsis</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>
