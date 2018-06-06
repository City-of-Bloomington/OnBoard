<?php
header('Content-type: application/json; charset=utf-8');
include BLOSSOM.'/errorMessages.php';
echo $this->includeBlocks();
