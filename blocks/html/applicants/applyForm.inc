<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 * @param Committee $this->committee (optional)
 */
use Application\Models\Applicant;
use Application\Models\CommitteeTable;
use Application\Models\Media;

$table = new CommitteeTable();
$committees = $table->find();

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
    <h1><?= $title ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/apply" id="applyForm" enctype="multipart/form-data">
        <fieldset><legend><?= $this->_('info_person'); ?></legend>
        <?php
            foreach (['firstname', 'lastname', 'email'] as $f) {
                echo $field->input(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f, 'required'=>true]);
            }

            echo $field->input(['name'=>'phone', 'id'=>'phone', 'label'=>$this->_('phone'), 'value'=>$phone, 'type'=>'tel']);

            foreach (['address', 'city', 'zip'] as $f) {
                echo $field->input(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f]);
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
                echo "
                <fieldset id=\"committees\">
                    <dl class=\"fn1-input-field--checkbox\">
                        <dt>{$this->_(['committee', 'committees', count($committees)])}</dt>
                ";
                    $chosen = [];
                    if (isset($_REQUEST['committees'])) { $chosen = $_REQUEST['committees']; }

                    foreach ($committees as $c) {
                        $checked = in_array($c->getId(), $chosen)
                            ? 'checked="true"'
                            : '';
                        $name = self::escape($c->getName());
                        echo "
                        <dd><label>
                                <input name=\"committees[]\" type=\"checkbox\" value=\"{$c->getId()}\" $checked />
                                <span>$name</span>
                            </label>
                        </dd>
                        ";
                    }
                echo "
                    </dl>
                </fieldset>
                ";
            }
        ?>

        <fieldset>
            <dl class="fn1-input-field">
                <dt><label><?= $this->_('citylimits'); ?></label></dt>
                <dd><label>
                        <input name="citylimits" type="radio" value="1" <?= $citylimits ? 'checked="true"' : ''; ?> />
                        <?= $this->_('yes'); ?>
                    </label>
                    <label>
                        <input name="citylimits" type="radio" value="0" <?= $citylimits ? '' : 'checked="true"'; ?> />
                        <?= $this->_('no'); ?>
                    </label>
                </dd>
            </dl>
            <?php
                echo $field->input(['name'=>'occupation', 'id'=>'occupation', 'label'=>$this->_('occupation'), 'value'=>$occupation]);
            ?>

            <dl class="fn1-input-field">
                <dt><label for="referredFrom"><?= $this->_('referredFrom'); ?></label></dt>
                <dd><select name="referredFrom" id="referredFrom"><option value=""></option>
                    <?php
                        $knownReferralOption = false;
                        foreach (Applicant::$referralOptions as $o) {
                            $selected = $referredFrom === $o
                                ? 'selected="true"'
                                : '';
                            echo "<option $selected>$o</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <?php
                echo $field->input(['name'=>'referredOther', 'id'=>'referredOther', 'label'=>$this->_('referredOther'), 'value'=>$referredOther]);
            ?>

        </fieldset>

        <fieldset><legend><?= $this->_('interest'); ?></legend>
            <textarea name="interest"><?= $interest; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('qualifications'); ?></legend>
            <textarea name="qualifications"><?= $qualifications; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('media'); ?></legend>
            <?php
                function return_bytes($size)
                {
                    switch (substr($size, -1)) {
                        case 'M': return (int)$size * 1048576;
                        case 'K': return (int)$size * 1024;
                        case 'G': return (int)$size * 1073741824;
                        default:  return (int)$size;
                    }
                }
                $upload_max_size  = ini_get('upload_max_filesize');
                    $post_max_size  = ini_get('post_max_size');
                $upload_max_bytes = return_bytes($upload_max_size);
                    $post_max_bytes = return_bytes(  $post_max_size);

                if ($upload_max_bytes < $post_max_bytes) {
                    $maxSize  = $upload_max_size;
                    $maxBytes = $upload_max_bytes;
                }
                else {
                    $maxSize  = $post_max_size;
                    $maxBytes = $post_max_bytes;
                }

                $accept = [];
                $extensions = array_keys(Media::$extensions);
                foreach ($extensions as $ext) {
                    $accept[] = ".$ext";
                }
                $accept = implode(',', $accept);

                $maxSize = sprintf($this->_('file_max_size',    'messages'), $maxSize);
                $accept  = sprintf($this->_('file_type_accept', 'messages'), $accept);
                echo "
                <input name=\"MAX_FILE_SIZE\" type=\"hidden\" value=\"$maxBytes\" />
                <input type=\"file\" name=\"mediafile\" id=\"mediafile\" accept=\"$accept\" />
                <div><i class=\"fa fa-info-circle\"></i> $maxSize</div>
                <div><i class=\"fa fa-info-circle\"></i> $accept</div>
                ";
            ?>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
