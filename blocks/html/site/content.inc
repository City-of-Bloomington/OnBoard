<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;
use Application\Models\Site;
use Michelf\MarkdownExtra;
?>
<section>
    <h1><?= $this->_('siteContent'); ?></h1>

    <?php
        $userCanEdit = Person::isAllowed('site', 'updateContent');
        $helper = $this->template->getHelper('buttonLink');

        $editButton = '';
        foreach (Site::$labels as $label) {
            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI."/site/updateContent?label=$label",
                    $this->_('content_edit'),
                    'edit'
                );
            }
            $markdown = new MarkdownExtra();
            $content = $markdown->transform(Site::getContent($label));

            echo "
            <article>
                <h1>{$this->_($label)}</h1>
                <div class=\"tools\">$editButton</div>
                <div>
                    $content
                </div>
            </article>
            ";
        }
    ?>
</section>
