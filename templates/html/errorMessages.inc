<?php
/**
 * @copyright 2018-2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Web\Block;

if (isset($_SESSION['errorMessages'])) {
	$errorBlock = new Block('errorMessages.inc', ['errorMessages'=>$_SESSION['errorMessages']]);
	echo $errorBlock->render($this->outputFormat);
	unset($_SESSION['errorMessages']);
}
