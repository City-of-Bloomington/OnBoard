<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicType $this->topicType
 */
use Blossom\Classes\View;

$name = View::escape($this->topicType->getName());
$title = $this->topicType->getId() ? $this->_('labels.edit_topicType') : $this->_('labels.add_topicType');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/topicTypes/update">
		<fieldset><legend><?php echo $name; ?></legend>
			<input name="topicType_id" type="hidden" value="<?php echo $this->topicType->getId(); ?>" />

			<table>
				<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/topicTypes');
			?>
		</fieldset>
	</form>
</div>
