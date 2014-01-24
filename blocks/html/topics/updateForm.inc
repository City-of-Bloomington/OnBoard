<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Topic $this->topic
 */
use Application\Models\TopicTypeTable;
use Application\Models\TagTable;
use Blossom\Classes\View;

$action = $this->topic->getId() ? 'edit' : 'add';
?>
<h2><?php echo $this->_("labels.{$action}_topic"); ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/topics/update">
	<fieldset><legend><?php echo $this->_('labels.info_topic'); ?></legend>
		<?php
			if ($this->topic->getId()) {
				echo "<input name=\"topic_id\" type=\"hidden\" value=\"{$this->topic->getId()}\" />";
			}
			else {
				echo "<input name=\"committee_id\" type=\"hidden\" value=\"{$this->topic->getCommittee_id()}\" />";
			}
		?>

		<table>
			<tr><td><label for="topicType_id" class="required">
						<?php echo $this->_(['labels.topicType', 'labels.topicTypes', 1]); ?>
					</label>
				</td>
				<td><select name="topicType_id" id="topicType_id">
					<?php
						$table = new TopicTypeTable();
						$options = $table->find();
						foreach ($options as $type) {
							$name = View::escape($type);
							$selected = $type->getId()==$this->topic->getTopicType_id()
										? ' selected="selected"'
										: '';
							echo "<option value=\"{$type->getId()}\"$selected>$name</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr><td><label for="date" class="required"><?php echo $this->_('labels.date'); ?></label></td>
				<td><input name="date" id="date" maxlength="10" size="10"
						value="<?php echo $this->topic->getDate(DATE_FORMAT); ?>" />
				</td>
			</tr>

			<tr><td><label for="number" class="required"><?php echo $this->_('labels.number'); ?></label></td>
				<td><input name="number" id="number"
							value="<?php echo View::escape($this->topic->getNumber()); ?>" />
				</td>
			</tr>

			<tr><td><label for="description" class="required"><?php echo $this->_('labels.description'); ?></label></td>
				<td><textarea name="description" id="description" rows="3" cols="60"><?php echo View::escape($this->topic->getDescription()); ?></textarea></td>
			</tr>

			<tr><td><label for="synopsis" class="required"><?php echo $this->_('labels.synopsis'); ?></label></td>
				<td><textarea name="synopsis" id="synopsis" rows="3" cols="60"><?php echo View::escape($this->topic->getSynopsis()); ?></textarea></td>
			</tr>

		</table>
	</fieldset>

	<fieldset><legend><?php echo $this->_(['labels.tag', 'labels.tags', 2]); ?></legend>
		<?php
			$table = new TagTable();
			$list = $table->find();
			foreach ($list as $tag) {
				$name = View::escape($tag->getName());
				$checked = $this->topic->hasTag($tag) ? 'checked="checked"' : '';
				echo "
				<label>
					<input type=\"checkbox\" name=\"tags[{$tag->getId()}]\" value=\"{$tag->getId()}\" $checked />
					$name
				</label>
				";
			}
		?>
	</fieldset>

	<fieldset><legend><?php echo $this->_('labels.submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($this->topic->getCommittee()->getUri());
		?>
	</fieldset>
</form>
