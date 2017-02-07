<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\Block;

$this->applications = $this->committee->getApplications(['current'=>time()]);
$seats = $this->committee->getSeats(['current'=>true]);

$count = count($this->applications);

$title = $this->_('applications_reportForm_title', 'messages');
?>
<section id="reportForm">
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="get" action="<?= BASE_URI; ?>/applications/report">
        <fieldset id="seats">
            <legend><?= $this->_(['seat', 'seats', count($seats)]); ?></legend>
            <?php
                foreach ($seats as $s) {
                    $classes = [];
                    if ($s->hasVacancy()) { $classes[] = 'vacant'; }
                    $classes  = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';

                    $code = self::escape($s->getCode());
                    $name = self::escape($s->getName());
                    echo "
                    <div $classes>
                        <label>
                            <input name=\"seats[]\" type=\"checkbox\" value=\"{$s->getId()}\" />
                            $code: $name
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
