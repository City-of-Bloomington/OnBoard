<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag
 */
use Blossom\Classes\View;

$name = View::escape($this->tag->getName());
$title = $this->tag->getId() ? $this->_('labels.edit_tag') : $this->_('labels.add_tag');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/tags/update">
		<fieldset><legend><?php echo $name; ?></legend>
			<input name="tag_id" type="hidden" value="<?php echo $this->tag->getId(); ?>" />

			<table>
				<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/tags');
			?>
		</fieldset>
	</form>
</div>
