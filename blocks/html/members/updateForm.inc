<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
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

	<form method="post">
        <fieldset>
        <input name="member_id"    type="hidden" value="<?= $id;           ?>" />
		<input name="term_id"      type="hidden" value="<?= $term_id;      ?>" />
		<input name="seat_id"      type="hidden" value="<?= $seat_id;      ?>" />
		<input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />
		<?php
            $helper = $this->template->getHelper('field');

            echo $helper->field([
                'name'     => 'person_id',
                'id'       => 'person_id',
                'label'    => $this->_('person'),
                'value'    => $this->member->getPerson(),
                'type'     => 'person',
                'required' => true
            ]);

            echo $helper->field([
                'name'     => 'startDate',
                'id'       => 'startDate',
                'label'    => $this->_('startDate'),
                'value'    => new \DateTime($this->member->getStartDate()),
                'type'     => 'date',
                'required' => true
            ]);

            $endDate = $this->member->getEndDate()
                     ? new \DateTime($this->member->getEndDate())
                     : null;

            echo $helper->field([
                'name'     => 'endDate',
                'id'       => 'endDate',
                'label'    => $this->_('endDate'),
                'value'    => $endDate,
                'type'     => 'date'
            ]);

			$helper = $this->template->getHelper('saveAndCancelButtons');
			$uri    = $seat_id
                    ? parent::generateUri('seats.view')."?seat_id=$seat_id"
                    : parent::generateUri('committees.members')."?committee_id=$committee_id";
			echo $helper->saveAndCancelButtons($uri);
		?>
        </fieldset>
	</form>
</section>
