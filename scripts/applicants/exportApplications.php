<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\Applicant;
use Application\Models\CommitteeTable;

$_SERVER['REQUEST_URI'] = __FILE__;
include '../../bootstrap.php';

$table      = new CommitteeTable();
$committees = $table->find(['current'=>true, 'takesApplications'=>true]);
foreach ($committees as $c) {
    $dir = "./applications/{$c->getName()}";
    if (!is_dir($dir)) { mkdir($dir, 0775, true); }

    foreach ($c->getApplications(['current'=>time()]) as $a) {
        $p  = $a->getApplicant();
        $md = renderMarkdown($p);

        file_put_contents("$dir/{$p->getFullname()}.txt", $md);

        foreach ($p->getFiles() as $f) {
            $filename = "{$p->getFullname()}.{$f->getExtension()}";
            copy($f->getFullpath(), "$dir/$filename");
        }
    }
}

function renderMarkdown(Applicant $p): string
{
    $cl = $p->getCityLimits() ? 'Yes' : 'No';
    return "{$p->getFullname()}
-----------
{$p->getEmail()}
{$p->getPhone()}
{$p->getAddress()}

## Do you live in the city limits?
$cl

## Occupation
{$p->getOccupation()}

## How did you hear of this opening?
{$p->getReferredFrom()}
{$p->getReferredOther()}

## Please explain your interest
{$p->getInterest()}

## Please describe your qualifications
{$p->getQualifications()}
";
}
