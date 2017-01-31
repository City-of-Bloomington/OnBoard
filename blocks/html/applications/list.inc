<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->applications
 * @param string         $this->title      (optional)
 * @param Committee      $this->committee  (optional)
 * @param Applicant      $this->applicant  (optional)
 * @param boolean        $this->selectable (optional)
 */
$title = $this->title ? self::escape($this->title) : $this->_(['application', 'applications', count($this->applications)]);
?>
<section class="applications_list">
    <header>
        <h1><?= $title; ?></h1>
    </header>
    <?php
        $this->_include('applications/partials/table.inc');
    ?>
</section>
