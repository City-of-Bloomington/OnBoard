<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 * @param Committee $this->committee (optional)
 */
use Application\Models\Applicant;
use Application\Models\Captcha;
use Application\Models\CommitteeTable;
use Application\Models\ApplicantFile;
use Application\Models\Site;
use Michelf\MarkdownExtra;


$fields = [
    'id', 'firstname', 'lastname', 'email', 'phone',
    'address', 'city', 'zip',
    'citylimits', 'occupation', 'referredFrom', 'referredOther', 'interest', 'qualifications'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}

$title = $this->committee
    ? sprintf($this->_('applyToCommittee', 'messages'), self::escape($this->committee->getName()))
    : $this->_('apply');

$field = $this->template->getHelper('field');
?>
<section>
    <header>
        <h1><?= $title ?></h1>
    </header>
    <div>
    <?php
        $markdown = new MarkdownExtra();
        echo $markdown->transform(Site::getContent('applyForm_help'));
    ?>
    </div>

    <form method="post" action="<?= BASE_URI; ?>/applicants/apply" id="applyForm" enctype="multipart/form-data">
        <fieldset><legend><?= $this->_('info_person'); ?></legend>
        <?php
            foreach (['firstname', 'lastname', 'email'] as $f) {
                echo $field->field(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f, 'required'=>true]);
            }

            echo $field->field(['name'=>'phone', 'id'=>'phone', 'label'=>$this->_('phone'), 'value'=>$phone, 'type'=>'tel']);

            foreach (['address', 'city', 'zip'] as $f) {
                echo $field->field(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f]);
            }
            if ($this->committee) {
                echo "
                <input name=\"committee_id\" type=\"hidden\" value=\"{$this->committee->getId()}\" />
                <input name=\"committees[]\" type=\"hidden\" value=\"{$this->committee->getId()}\" />
                ";
            }
        ?>
        </fieldset>
        <?php
            if (!$this->committee) {
                echo '<fieldset id="committees">';
                $options = [];
                $table   = new CommitteeTable();
                $list    = $table->find(['current'=>true]);
                foreach ($list as $c) {
                    $options[] = ['value'=>$c->getId(), 'label'=>parent::escape($c->getName())];
                }
                echo $field->field([
                    'name'    => 'committees',
                    'label'   => $this->_(['committee', 'committees', count($list)]),
                    'value'   => !empty($_REQUEST['committees']) ? $_REQUEST['committees'] : [],
                    'type'    => 'checkbox',
                    'options' => $options
                ]);
                echo '</fieldset>';
            }
        ?>

        <fieldset>
        <?php
            echo $field->field([
                'name' => 'citylimits',
                'label' => $this->_('citylimits'),
                'value' => $citylimits,
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'label' => $this->_('yes')],
                    ['value' => 0, 'label' => $this->_('no' )]
                ]
            ]);

            echo $field->field(['name'=>'occupation', 'id'=>'occupation', 'label'=>$this->_('occupation'), 'value'=>$occupation]);

            $options = [['value'=>'']];
            foreach (Applicant::$referralOptions as $o) { $options[] = ['value'=>$o]; }
            echo $field->field([
                'id'      => 'referredFrom',
                'name'    => 'referredFrom',
                'label'   => $this->_('referredFrom'),
                'type'    => 'select',
                'value'   => $referredFrom,
                'options' => $options
            ]);

            echo $field->field(['name'=>'referredOther', 'id'=>'referredOther', 'label'=>$this->_('referredOther'), 'value'=>$referredOther]);
        ?>

        </fieldset>

        <fieldset><legend><?= $this->_('interest'); ?></legend>
            <textarea name="interest"><?= $interest; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('qualifications'); ?></legend>
            <textarea name="qualifications"><?= $qualifications; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('applicantFile'); ?></legend>
            <?php
                $accept = [];
                foreach (ApplicantFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }
                $accept = implode(',', $accept);

                echo "<input type=\"file\" name=\"applicantFile\" id=\"applicantFile\" accept=\"$accept\" />";
                $this->_include('files/partials/uploadHelp.inc');
            ?>
        </fieldset>

        <fieldset>
        <?php
            Captcha::render();

            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
