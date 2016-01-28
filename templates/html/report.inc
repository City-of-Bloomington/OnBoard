<?
/*
    This file is meant as an HTML mockup. It is still a work in progress as of 1/28/2015. DH
*/
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/local/report.css" />
</head>
<body>
    <section class="report-page">
        <h1>City of Bloomington Public Board Applicant Report</h1>
        <h2>Table of Contents</h2>
        <ol>
            <li>
                Public Applicants
                <ol>
                    <li><a href="#">Firstname Surname application</a></li>
                    <li><a href="#">Primary Secondary application</a></li>
                    <li><a href="#">First Last application</a></li>
                </ol>
            </li>
            <li>
                Requested Seat Requirements
                <ol>
                    <li><a href="#">C-1</a></li>
                    <li><a href="#">C-2</a></li>
                    <li><a href="#">M-1</a></li>
                    <li><a href="#">M-2</a></li>
                </ol>
            </li>
        </ol>
    </section>
    <section class="report-page">
        <h1 id="application-<?= 'applicantId' ?>">Firstname Surname application</h1>
        <dl class="fn1-output-field"> <? /* Maybe use FN1, or maybe use unique report CSS? */ ?>
            <dt><?= 'key' ?></dt>
            <dd><?= 'value' ?></dd>
        </dl>
    </section>
</body>
</html>
