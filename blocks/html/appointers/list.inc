<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->appointers
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<section class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h1 class="fn1-uiBlock-title"><?php
        echo $this->translate(['appointer','appointers',2]);
      ?>
    </h1>
    <div class="fn1-uiBlock-headerTools">
      <?php
        $helper = $this->template->getHelper('buttonLink');
        if (Person::isAllowed('appointers')) {
          echo $helper->buttonLink(
            BASE_URI.'/appointers/update',
            $this->translate('add_appointer'),
            'add'
          );
        }
      ?>
    </div>
  </header>
  <div class="fn1-uiBlock-content">
    <table>
      <?php
        foreach ($this->appointers as $appointer) {
          $editButton = '';
          if (Person::isAllowed('appointers')) {
            $editButton = $helper->buttonLink(
              BASE_URI.'/appointers/update?appointer_id='.$appointer->getId(),
              $this->translate('edit'),
              'edit',
              ButtonLink::SIZE_ICON
            );
          }

          $name = View::escape($appointer->getName());
          echo "
          <tr><td>$editButton</td>
            <td>$name</td>
          </tr>
          ";
        }
      ?>
    </table>
  </div>
</section>
