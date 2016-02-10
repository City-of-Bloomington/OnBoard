<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 */
$fields = ['id', 'firstname', 'lastname', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}

$title = $id ? $this->_('applicant_edit') : $this->_('applicant_add');
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/update">
        <fieldset>
            <input name="applicant_id" type="hidden" value="<?= $id; ?>" />

            <?php
                include APPLICATION_HOME.'/blocks/html/people/partials/personalInfoFields.inc';
            ?>

        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
