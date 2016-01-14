<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
?>
<div class="onboard-breadcrumbs">
  <ul><li><a href="<?= BASE_URI; ?>"><?= $this->_('home'); ?></a></li>
    <li><a href="<?= BASE_URI; ?>/committees"><?= $this->_(['committee','committees',2]); ?></a></li>
    <?php
      if (isset($this->committee)) {
        $url = BASE_URI.'/committees/members?committee_id='.$this->committee->getId();

        $name = self::escape($this->committee->getName());

        echo "<li><a href=\"$url\">$name</a></li>";
      }
    ?>
  </ul>
</div>
