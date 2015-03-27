<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$FILES = fopen('./files.txt','w');

$xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE properties SYSTEM "http://java.sun.com/dtd/properties.dtd">
<properties>
    <entry key="type">cm:folder</entry>
</properties>
EOT;

function prep(DirectoryIterator $dir)
{
    global $xml;
    global $FILES;

    foreach ($dir as $f) {
        if (!$f->isDot()) {
            if ($f->isDir()) {
                $d = "{$f->getPath()}/{$f->getFilename()}";
                file_put_contents("$d.metadata.properties.xml", $xml);
                prep(new DirectoryIterator($d));
            }
            else {
                if ($f->getExtension() != 'xml') {
                    fwrite($FILES, $f->getPath().'/'.$f->getFilename()."\n");
                }
            }
        }
    }
}
prep(new DirectoryIterator(__DIR__.'/files/Agendas'));

fclose($FILES);