<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Member $this->member
 */
$fields = ['id', 'committee_id', 'seat_id', 'term_id', 'person_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->member->$get();
}

$title = $this->member->getId() ? $this->_('member_edit') : $this->_('member_add');

?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <?php
            if ($term_id) {
                $term = $this->member->getTerm();
                echo "<h2>{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}</h2>";
            }
        ?>
    </header>

	<form method="post" action="<?= BASE_URI; ?>/members/update">
        <input name="member_id"    type="hidden" value="<?= $id;           ?>" />
		<input name="term_id"      type="hidden" value="<?= $term_id;      ?>" />
		<input name="seat_id"      type="hidden" value="<?= $seat_id;      ?>" />
		<input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />

		<dl class="fn1-input-field--select">
            <dt><label for="person_id" class="required"><?= $this->_(['person', 'people', 1]); ?></label></dt>
			<dd><?php
					$person = $this->member->getPerson();
					$helper = $this->template->getHelper('personChooser');
					echo $helper->personChooser('person_id', 'person_id', $person);
				?>
			</dd>
		</dl>
		<dl class="fn1-input-field">
            <dt><label  for="startDate" class="required"><?= $this->_('startDate'); ?></label></dt>
			<dd><?php
                    $helper = $this->template->getHelper('datePicker');
                    echo $helper->datePicker('startDate', 'startDate', $this->member->getStartDate('U'));
                ?>
			</dd>
		</dl>
		<dl class="fn1-input-field">
            <dt><label  for="endDate"><?= $this->_('endDate'); ?></label></dt>
			<dd><?= $helper->datePicker('endDate', 'endDate', $this->member->getEndDate('U')); ?></dd>
		</dl>
        <div class="fn1-btn-container">
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');

			$uri = BASE_URI.'/seats/view?seat_id='.$seat_id;
			echo $helper->saveAndCancelButtons($uri);
		?>
        </div>
	</form>
</section>