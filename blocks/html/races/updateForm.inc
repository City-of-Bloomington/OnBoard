<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Race $this->race
 */
use Blossom\Classes\View;

$name = View::escape($this->race->getName());
$title = $this->race->getId() ? $this->_('labels.edit_race') : $this->_('labels.add_race');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/races/update">
		<fieldset><legend><?php echo $name; ?></legend>
			<input name="race_id" type="hidden" value="<?php echo $this->race->getId(); ?>" />

			<table>
				<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/races');
			?>
		</fieldset>
	</form>
</div>
