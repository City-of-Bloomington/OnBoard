<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\Block;

$this->applications = $this->committee->getApplications(['current'=>time()]);
$seats = $this->committee->getSeats(time());

$count = count($this->applications);

$title = sprintf($this->_('applications_reportForm_title', 'messages'), self::escape($this->committee->getName()));
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="get" action="<?= BASE_URI; ?>/applications/report" id="reportForm">
        <fieldset id="seats">
            <legend><?= $this->_(['seat', 'seats', count($seats)]); ?></legend>
            <?php
                foreach ($seats as $s) {
                    $code = self::escape($s->getCode());
                    echo "
                    <div>
                        <label>
                            <input name=\"seats[]\" type=\"checkbox\" value=\"{$s->getId()}\" />
                            $code
                        </label>
                    </div>
                    ";
                }
            ?>
        </fieldset>
        <fieldset id="applicants">
            <legend><?= $this->_(['applicant', 'applicants', $count]); ?></legend>
            <?php
                $this->selectable = true;
                $this->type = 'current';
                $this->_include('applications/partials/table.inc');
            ?>
        </fieldset>
        <fieldset>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />
            <button type="submit"><?= $this->_('report_view'); ?></button>
        </fieldset>
    </form>
</section>
