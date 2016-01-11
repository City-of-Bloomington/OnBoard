<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee (optional)
 */
?>
<div class="breadcrumbs">
	<ul><li><a href="<?= BASE_URI; ?>"><?= $this->_('home'); ?></a></li>
		<li><a href="<?= BASE_URI; ?>/committees"><?= $this->_(['committee','committees',2]); ?></a></li>
		<?php
			if (isset($this->committee)) {
				$info = BASE_URI.'/committees/info?committee_id='.$this->committee->getId();

				$name = self::escape($this->committee->getName());

				echo "<li><a href=\"$info\">$name</a></li>";
			}
		?>
	</ul>
</div>
<?php
    if (isset($this->committee)) {
        $members = BASE_URI.'/committees/members?committee_id='.$this->committee->getId();
        $seats   = BASE_URI.'/committees/seats?committee_id='.$this->committee->getId();

        echo "
        <div>
            <h1>$name</h1>
            <nav>
                <a href=\"$info\">{$this->_('committee_info')}</a>
                <a href=\"$members\">{$this->_(['member', 'members', 2])}</a>
                <a href=\"$seats\">{$this->_(['seat', 'seats', 2])}</a>
            </nav>
        </div>
        ";
    }
?>
