<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->committees
 */
namespace Application\Models;

use Application\Templates\Helpers\ButtonLink;

$userCanEdit = Person::isAllowed('committees', 'edit');
?>
<section class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h1 class="fn1-uiBlock-title"><?php
        echo sprintf('%s, %s, %s, &amp; %s',
          $this->_(['council'   , 'councils'   , 2]),
          $this->_(['commission', 'commissions', 2]),
          $this->_(['committee' , 'committees' , 2]),
          $this->_(['board'     , 'boards'     , 2])
        );
      ?></h2>
      <div class="fn1-uiBlock-headerTools">
        <?php
          if ($userCanEdit) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
              BASE_URI.'/committees/update',
              $this->_('committee_add'),
              'add'
            );
          }
        ?>
      </div>
  </header>
  <div class="fn1-uiBlock-content">
    <ul>
      <?php
        foreach ($this->committees as $c) {
          $name = self::escape($c->getName());
          $url = BASE_URI.'/committees/members?committee_id='.$c->getId();
          echo "
            <li><a href=\"$url\">$name</a></li>
          ";
        }
      ?>
    </ul>
  </div>
</section>
