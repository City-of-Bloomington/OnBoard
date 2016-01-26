<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;
?>
<div     class="fn1-nav1">
    <nav class="fn1-nav1-container">
        <a href="<?= BASE_URI; ?>/committees"><?= $this->_(['committee', 'committees', 2]); ?></a>
        <a href="<?= BASE_URI; ?>/people"><?= $this->_(['person', 'people', 2]); ?></a>
        <a href="<?= BASE_URI; ?>/seats"><?= $this->_(['seat', 'seats', 2]); ?></a>
        <a href="<?= BASE_URI; ?>/seats/vacancies"><?= $this->_(['vacancy', 'vacancies', 2]); ?></a>
        <?php
            if (defined('VOTE_TRACKING') && VOTE_TRACKING) {
                $routes = [
                    'voteType'  => 'voteTypes',
                    'topic'     => 'topics',
                    'topicType' => 'topicTypes'
                ];
                foreach ($routes as $singular => $plural) {
                    if (Person::isAllowed($plural, 'index')) {
                        $uri = BASE_URI."/$plural";
                        echo "<a href=\"$uri\">{$this->_(['$singular', '$plural', 2])}</a>";
                    }
                }
            }
        ?>
    </nav>
</div>
