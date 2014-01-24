<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag
 */
use Blossom\Classes\View;

$name = View::escape($this->tag);
echo "<h2>$name</h2>";
