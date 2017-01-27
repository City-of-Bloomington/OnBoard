<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;

$name = self::escape("{$this->applicant->getFirstname()} {$this->applicant->getLastname()}");
?>
<section>
    <header>
        <h1><?= $name; ?></h1>
        <div class="tools">
        <?php
            if (!$this->disableButtons && Person::isAllowed('applicants', 'edit')) {
                $helper = $this->template->getHelper('buttonLink');
                echo $helper->buttonLink(
                    BASE_URI.'/applicants/update?applicant_id='.$this->applicant->getId(),
                    $this->_('applicant_edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>

    <?php
        $fields = [
            'email', 'phone', 'address', 'city', 'zip',
            'citylimits', 'occupation', 'referredFrom', 'referredOther',
            'interest', 'qualifications'
        ];
        foreach ($fields as $f) {
            $get = 'get'.ucfirst($f);
            switch ($f) {
                case 'citylimits':
                    $$f = $this->applicant->getCitylimits() ? $this->_('yes') : $this->_('no');
                    break;
                default:
                    $$f = self::escape($this->applicant->$get());
            }

            echo "
            <dl class=\"fn1-output-field\">
                <dt>{$this->_($f)}</dt>
                <dd>{$$f}</dd>
            </dl>
            ";
        }
        if (Person::isAllowed('applicantFiles', 'download')) {
            echo "
            <dl class=\"fn1-output-field\">
                <dt>{$this->_('applicantFile')}</dt><dd>
            ";
                $files = $this->applicant->getFiles();
                foreach ($files as $f) {
                    $uri = BASE_URI.'/applicantFiles/download?applicantFile_id='.$f->getId();
                    $filename = self::escape($f->getFilename());
                    echo "<a href=\"$uri\" class=\"{$f->getMime_type()}\">$filename</a>";
                }
            echo "
                </dd>
            </dl>
            ";
        }
    ?>
</section>
