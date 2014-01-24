<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param VoteType $this->voteType
 */
use Blossom\Classes\View;

$name = View::escape($this->voteType->getName());
$order = (int)$this->voteType->getOrdering();
$title = $this->voteType->getId() ? $this->_('labels.edit_voteType') : $this->_('labels.add_voteType');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/voteTypes/update">
		<fieldset><legend><?php echo $name; ?></legend>
			<input name="voteType_id" type="hidden" value="<?php echo $this->voteType->getId(); ?>" />

			<table>
				<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
				<tr><td><label for="ordering" class="required"><?php echo $this->_('labels.order'); ?></label></td>
					<td><input name="ordering" id="ordering" value="<?php echo $order; ?>" /></td>
				</tr>
			</table>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/voteTypes');
			?>
		</fieldset>
	</form>
</div>
