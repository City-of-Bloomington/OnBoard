<?php
$_SERVER['SITE_HOME'] = __DIR__.'/data';
include realpath(__DIR__.'/../Web/bootstrap.php');

$GLOBALS['ROUTES'   ] = $ROUTES;
$GLOBALS['DATABASES'] = $DATABASES;
