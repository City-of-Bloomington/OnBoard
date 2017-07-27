<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
$committee = parent::escape($this->legislation->getCommittee()->getName());
$type      = parent::escape($this->legislation->getType()     ->getName());
$number    = parent::escape($this->legislation->getName());
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
</section>
