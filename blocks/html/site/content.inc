<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Person;
use Application\Models\Site;
use Michelf\MarkdownExtra;
?>
<section>
    <header>
        <h1><?= $this->_('siteContent'); ?></h1>
    </header>
    <?php
        $userCanEdit = Person::isAllowed('site', 'updateContent');
        $helper      = $this->template->getHelper('buttonLink');
        $editButton  = '';
        foreach (Site::$labels as $label) {
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    parent::generateUri('site.updateContent')."?label=$label",
                    $this->_('content_edit'),
                    'edit'
                );
            }
            $markdown = new MarkdownExtra();
            $content  = $markdown->transform(Site::getContent($label));
            echo "
            <article>
                <header>
                    <h1>{$this->_($label)}</h1>
                    <div class=\"tools\">$editButton</div>
                </header>
                <div>
                    $content
                </div>
            </article>
            ";
        }
    ?>
</section>
