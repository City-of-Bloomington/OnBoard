<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 */
$fields = ['committee_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->member->$get();
}
?>
<section    class="fn1-uiBlock">
    <div    class="fn1-uiBlock-content">
        <form method="post" action="<?= BASE_URI; ?>/seats/appoint">
            <fieldset>
                <input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />
            </fieldset>
        </form>
    </div>
</section>