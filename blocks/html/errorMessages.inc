<?php
/**
 * @copyright 2007-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->errorMessages
 */
?>
<section id="errorMessages">
    <header>
        <h1><?= $this->_('error'); ?></h1>
    </header>
	<?php
        $errorDir = "/blocks/html/errorMessages";

		foreach ($this->errorMessages as $error) {
			$file = "$errorDir/$error.inc";

            if ($this->theme
                && is_file($this->theme.$file)) {
                include    $this->theme.$file;
            }
			elseif (is_file(APPLICATION_HOME.$file)) {
                include     APPLICATION_HOME.$file;
			}
			else {
				echo "<p>{$this->_($error, 'errors')}</p>";
			}
		}
	?>
</section>
