<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Applicant $this->applicant
 */
use Application\Models\ApplicantFile;

$fields = ['id', 'firstname', 'lastname', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}

$title = $id ? $this->_('applicant_edit') : $this->_('applicant_add');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="post" enctype="multipart/form-data">
        <fieldset><legend><?= $this->_('info_person'); ?></legend>
            <input name="applicant_id" type="hidden" value="<?= $id; ?>" />
            <?php
                $fields = ['firstname', 'lastname', 'email', 'phone'];
                foreach ($fields as $f) {
                    $get = 'get'.ucfirst($f);
                    $$f = self::escape($this->applicant->$get());
                }
                $field = $this->template->getHelper('field');
                echo $field->field(['name'=>'firstname', 'id'=>'firstname', 'label'=>$this->_('firstname'), 'value'=>$firstname, 'required'=>true]);
                echo $field->field(['name'=>'lastname',  'id'=>'lastname',  'label'=>$this->_('lastname'),  'value'=>$lastname,  'required'=>true]);
                echo $field->field(['name'=>'email',     'id'=>'email',     'label'=>$this->_('email'),     'value'=>$email,     'type'=>'email']);
                echo $field->field(['name'=>'phone',     'id'=>'phone',     'label'=>$this->_('phone'),     'value'=>$phone,     'type'=>'tel'  ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('applicantFile'); ?></legend>
        <?php
            $accept = [];
            foreach (ApplicantFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
            $accept = implode(',', $accept);

            foreach ($this->applicant->getFiles() as $f) {
                $file_id  = $f->getId();
                $uri      = parent::generateUri('applicantFiles.download').'?applicantFile_id='.$file_id;
                $filename = self::escape($f->getFilename());
                $filename = "<a href=\"$uri\" class=\"{$f->getMime_type()}\">$filename</a>";
                echo "
                <dl><dt><label for=\"applicantFile_$file_id\">$filename</label></dt>
                    <dd><input type=\"file\" name=\"applicantFile[$file_id]\" id=\"applicantFile_$file_id\" accept=\"$accept\" /></dd>
                </dl>
                ";

            }
            $this->_include('files/partials/uploadHelp.inc');
        ?>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(parent::generateUri('applicants.view')."?applicant_id=$id");
        ?>
        </fieldset>
    </form>
</section>
