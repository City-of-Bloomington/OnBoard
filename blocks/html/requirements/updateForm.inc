<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Requirement $this->requirement
 */
use Blossom\Classes\View;

$text = View::escape($this->requirement->getText());
$title = $this->requirement->getId() ? $this->_('labels.edit_requirement') : $this->_('labels.add_requirement');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/requirements/update">
		<fieldset><legend><?php echo $this->_(['labels.requirement','labels.requirements',1]); ?></legend>
			<input name="requirement_id" type="hidden" value="<?php echo $this->requirement->getId(); ?>" />

			<div>
				<textarea name="text" id="text" rows="5" cols="60"><?php echo $text; ?></textarea>
			</div>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/requirements');
			?>
		</fieldset>
	</form>
</div>
