<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
$fields = ['id', 'allocation_id', 'name'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->seat->$get();
}

$title = $this->seat->getId() ? $this->_('seat_edit') : $this->_('seat_add');
?>
<h2><?= $title; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/seats/update">
	<fieldset><legend><?php echo $this->_('info_seat'); ?></legend>
        <input name="seat_id"       type="hidden" value="<?= $id; ?>" />
        <input name="allocation_id" type="hidden" value="<?= $allocation_id; ?>" />

		<table>
			<tr><td><label  for="name" class="required"><?= $this->_('name'); ?></label></td>
				<td><input name="name" id="name" value="<?= $name; ?>" /></td>
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
        </table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI."/allocations/view?allocation_id=$allocation_id");
		?>
	</fieldset>
</form>
