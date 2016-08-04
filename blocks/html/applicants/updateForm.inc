<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 */
use Application\Models\Media;

$fields = ['id', 'firstname', 'lastname', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}

$title = $id ? $this->_('applicant_edit') : $this->_('applicant_add');
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/update" enctype="multipart/form-data">
        <fieldset><legend><?= $this->_('info_person'); ?></legend>
            <input name="applicant_id" type="hidden" value="<?= $id; ?>" />

            <?php
                $this->person = $this->applicant;
                $this->_include('people/partials/personalInfoFields.inc');
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('media'); ?></legend>
        <?php
            $accept = [];
            foreach (Media::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
            $accept = implode(',', $accept);

            foreach ($this->applicant->getMedia() as $m) {
                $media_id = $m->getId();
                $uri      = BASE_URI.'/media/download?media_id='.$media_id;
                $filename = self::escape($m->getFilename());
                $filename = "<a href=\"$uri\" class=\"{$m->getMime_type()}\">$filename</a>";
                echo "
                <dl><dt><label for=\"mediafile_$media_id\">$filename</label></dt>
                    <dd><input type=\"file\" name=\"mediafile[$media_id]\" id=\"mediafile_$media_id\" accept=\"$accept\" /></dd>
                </dl>
                ";

            }
            $this->_include('media/partials/uploadHelp.inc');
        ?>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI.'/applicants/view?applicant_id='.$id);
        ?>
        </fieldset>
    </form>
</section>
