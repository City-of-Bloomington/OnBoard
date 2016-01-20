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

            <dl class="fn1-input-field--select">
                <dt><label  for="type" class="required"><?= $this->_('type'); ?></label></dt>
                <dd><select name="type">
                    <?php
                        foreach (Seat::$types as $t) {
                            $selected = $t === $type
                                ? 'selected="true"'
                                : '';
                            echo "<option $selected>$t</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label  for="name" class="required"><?= $this->_('name'); ?></label></dt>
                <dd><input name="name" id="name" value="<?= $name; ?>" /></dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label   for="appointer_id" class="required"><?= $this->_(['appointer', 'appointers', 1]); ?></label></dt>
                <dd><select name="appointer_id" id="appointer_id"><option value=""></option>
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
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label  for="startDate" class="required"><?= $this->_('startDate'); ?></label></dt>
                <dd><input name="startDate" id="startDate"
                        value="<?= $this->seat->getStartDate(DATE_FORMAT); ?>" />
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label  for="endDate"><?= $this->_('endDate'); ?></label></dt>
                <dd><input name="endDate" id="endDate"
                        value="<?= $this->seat->getEndDate(DATE_FORMAT); ?>" />
                </dd>
            </dl>
            <dl class="fn1-input-field--select">
                <dt><label   for="termLength"><?= $this->_('termLength'); ?></label></dt>
                <dd><select name="termLength" id="termLength">
                    <?php
                        foreach (Seat::$termIntervals as $interval => $label) {
                            $selected = $termLength === $interval
                                ? 'selected="true"'
                                : '';
                            echo "<option value=\"$interval\" $selected>$label</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
        </fieldset>

        <fieldset><legend><?= $this->_(['requirement', 'requirements', 2]); ?></legend>
            <textarea name="requirements" class="markdown"><?= $requirements; ?></textarea>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            $uri = $id
                ? BASE_URI."/seats/view?seat_id=$id"
                : BASE_URI."/committees/seats?committee_id=$committee_id";
            echo $helper->saveAndCancelButtons($uri);
        ?>
        </fieldset>
    </form>
  </div>
</section>
