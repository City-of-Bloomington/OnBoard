<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->applications
 * @param array $this->seats
 * @param Committee $this->committee
 */
use Blossom\Classes\Block;
?>
<section>
    <header>
        <?php $this->_include('html/committees/partials/committeeVacancyHeading.inc'); ?>
        <nav class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI."/committees/applications?committee_id={$this->committee->getId()}",
                $this->_('applications_back'),
                'back'
            );
        ?>
        </nav>
    </header>
    <?php
        $this->_include('html/seats/requirements.inc');

        foreach ($this->applications as $a) {
            $block = new Block('applicants/info.inc', [
                'applicant'      => $a->getApplicant(),
                'disableButtons' => true
            ]);
            echo $block->render('html', $this->template);
        }
    ?>
</section>