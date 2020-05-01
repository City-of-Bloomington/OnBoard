<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->applications
 * @param array $this->seats
 * @param Committee $this->committee
 */
use Web\Block;
?>
<section>
    <header>
        <?php $this->_include('committees/partials/committeeVacancyHeading.inc'); ?>
        <nav class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                parent::generateUri('committees.applications')."?committee_id={$this->committee->getId()}",
                $this->_('applications_back'),
                'back'
            );
        ?>
        </nav>
    </header>
    <?php
        if (count($this->seats)) {
            $this->_include('seats/requirements.inc');
        }
        else {
            echo "<p>{$this->_('noSeatsSelected', 'messages')}</p>";
        }

        if (count($this->applications)) {
            foreach ($this->applications as $a) {
                $block = new Block('applicants/info.inc', [
                    'applicant'      => $a->getApplicant(),
                    'disableButtons' => true
                ]);
                echo $block->render('html', $this->template);
            }
        }
        else {
            echo "<p>{$this->_('noApplicationsSelected', 'messages')}</p>";
        }
    ?>
</section>
