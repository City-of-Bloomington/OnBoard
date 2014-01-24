<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$fields = ['firstname','lastname','email'];
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$get());
}
$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : 'Add Someone';
?>
<div class="updatePersonForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/people/update">
		<fieldset><legend><?php echo $this->_('labels.info_person'); ?></legend>
			<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />

			<table>
				<tr><td><label for="firstname" class="required"><?php echo $this->_('labels.firstname'); ?></label></td>
					<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
				</tr>

				<tr><td><label for="lastname" class="required"><?php echo $this->_('labels.lastname'); ?></label></td>
					<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
				</tr>

				<tr><td><label for="email"><?php echo $this->_(['labels.email','labels.emails',1]); ?></label></td>
					<td><input name="email" id="email" value="<?php echo $email; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/people');
			?>
		</fieldset>
	</form>
</div>
