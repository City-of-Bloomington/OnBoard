<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\Url;
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header"><h1><?= $this->_(['current_member', 'current_members', 2]); ?></h1></header>
    <div    class="fn1-uiBlock-content">
    <?php
        $committee_id = $this->committee->getId();

        $partial = $this->committee->getType()."Members.inc";
        $this->_include("html/committees/partials/$partial");
    ?>
    </div>
</section>
