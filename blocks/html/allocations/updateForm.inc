<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Allocation $this->allocation
 */
use Application\Models\AppointerTable;

$fields = ['id', 'committee_id', 'appointer_id', 'name', 'requirements'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->allocation->$get());
}

$title = $this->allocation->getId() ? $this->_('allocation_edit') : $this->_('allocation_add');
?>
<h2><?= $title; ?></h2>
<form method="post" action="<?= BASE_URI; ?>/allocations/update">
	<fieldset><legend><?= $this->_('allocation_info'); ?></legend>
        <input name="allocation_id" type="hidden" value="<?= $id; ?>" />
        <input name="committee_id"  type="hidden" value="<?= $committee_id; ?>" />

		<table>
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

			<tr><td><label  for="startDate"><?= $this->_('startDate'); ?></label></td>
				<td><input name="startDate" id="startDate" value="<?= $this->allocation->getStartDate(DATE_FORMAT); ?>" /></td>
			</tr>
			<tr><td><label  for="endDate"><?= $this->_('endDate'); ?></label></td>
				<td><input name="endDate" id="endDate" value="<?= $this->allocation->getEndDate(DATE_FORMAT); ?>" /></td>
			</tr>

		</table>
		<div>
			<div><label for="requirements"><?= $this->_(['requirement', 'requirements', 2]); ?></label></div>
			<textarea  name="requirements" id="requirements" class="markdown"><?= $requirements; ?></textarea>
		</div>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI."/committees/view?committee_id=$committee_id");
		?>
	</fieldset>
</form>
