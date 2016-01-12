<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\AppointerTable;
use Application\Models\Seat;

$fields = ['id', 'type', 'committee_id', 'appointer_id', 'name', 'requirements', 'termLength'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->seat->$get();
}

$title = $this->seat->getId() ? $this->_('seat_edit') : $this->_('seat_add');
?>
<section  class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h1   class="fn1-uiBlock-title"><?= $title; ?></h1>
  </header>
  <div    class="fn1-uiBlock-content">
    <form method="post" action="<?php echo BASE_URI; ?>/seats/update">
      <fieldset><legend><?php echo $this->_('info_seat'); ?></legend>
            <input name="seat_id"       type="hidden" value="<?= $id; ?>" />
            <input name="committee_id"  type="hidden" value="<?= $committee_id; ?>" />

            <table>
                <tr><td><label  for="type" class="required"><?= $this->_('type'); ?></label></td>
                    <td><select name="type">
                        <?php
                            foreach (Seat::$types as $t) {
                                $selected = $t === $type
                                    ? 'selected="true"'
                                    : '';
                                echo "<option $selected>$t</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr><td><label  for="name" class="required"><?= $this->_('name'); ?></label></td>
                    <td><input name="name" id="name" value="<?= $name; ?>" /></td>
                </tr>
                <tr><td><label   for="appointer_id" class="required"><?= $this->_(['appointer', 'appointers', 1]); ?></label></td>
                    <td><select name="appointer_id" id="appointer_id"><option value=""></option>
                        <?php
                            $table = new AppointerTable();
                            $list = $table->find();
                            foreach ($list as $a) {
                                $name = self::escape($a->getName());
                                $selected = ($appointer_id === $a->getId())
                                            ? 'selected="true"'
                                            : '';
                                echo "<option value=\"{$a->getId()}\" $selected>$name</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr><td><label  for="startDate" class="required"><?= $this->_('startDate'); ?></label></td>
                    <td><input name="startDate" id="startDate"
                            value="<?= $this->seat->getStartDate(DATE_FORMAT); ?>" />
                    </td>
                </tr>
                <tr><td><label  for="endDate"><?= $this->_('endDate'); ?></label></td>
                    <td><input name="endDate" id="endDate"
                            value="<?= $this->seat->getEndDate(DATE_FORMAT); ?>" />
                    </td>
                </tr>
                <tr><td><label   for="termLength"><?= $this->_('termLength'); ?></label></td>
                    <td><select name="termLength" id="termLength">
                        <?php
                            foreach (Seat::$termIntervals as $interval => $label) {
                                $selected = $termLength === $interval
                                    ? 'selected="true"'
                                    : '';
                                echo "<option value=\"$interval\" $selected>$label</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div>
                <div><label for="requirements"><?= $this->_(['requirement', 'requirements', 2]); ?></label></div>
                <textarea  name="requirements" id="requirements" class="markdown"><?= $requirements; ?></textarea>
            </div>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI."/committees/seats?committee_id=$committee_id");
            ?>
        </fieldset>
    </form>
  </div>
</section>
