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
?>
<section>
    <h1><?= $this->_(['application', 'applications', $count]); ?></h1>

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
                $this->_include('html/applications/partials/table.inc');
            ?>
        </fieldset>
        <fieldset>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />
            <button type="submit"><?= $this->_('report'); ?></button>
        </fieldset>
    </form>
</section>
