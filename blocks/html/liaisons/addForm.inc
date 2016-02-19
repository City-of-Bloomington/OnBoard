<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
?>
<section>
    <h1><?= $this->_('liaison_add'); ?></h1>
    <form method="post" action="<?= BASE_URI; ?>/liaisons/add">
        <fieldset>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />
            <dl class="fn1-input-field--select">
                <dt><label for="person_id"><?= $this->_('person'); ?></label></dt>
                <dd><?php
                        $person = null;
                        if (!empty($_REQUEST['person_id'])) {
                            try { $person = new Person($_REQUEST['person_id']); }
                            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                        }
                        $helper = $this->template->getHelper('personChooser');
                        echo $helper->personChooser('person_id', 'person_id', $person);
                    ?>
                </dd>
            </dl>

            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/info?committee_id='.$this->committee->getId()
                );
            ?>
        </fieldset>
    </form>
</section>
