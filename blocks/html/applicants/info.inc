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

    <table>
    <?php
        $fields = ['email', 'phone', 'address', 'city', 'zip'];
        foreach ($fields as $f) {
            $get = 'get'.ucfirst($f);
            $$f = self::escape($this->applicant->$get());
            echo "
            <tr><td>{$this->_($f)}</td><td>{$$f}</td></tr>
            ";
        }
    ?>
    </table>
</section>
