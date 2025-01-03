<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Site;
use Michelf\Markdown;
?>
<section>
    <header>
        <h1><?= $this->_('thanks'); ?></h1>
    </header>
    <?php
        $m = Site::getContent('application_received');
        echo Markdown::defaultTransform($m);
    ?>
</section>
