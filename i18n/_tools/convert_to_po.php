<?php

/**
 * Simple Array to PO converter for TBG
 */
define('__NEWLINE__', PHP_EOL);

/**
 * Checking arguments
 */
if ($argc < 4) {
    echo 'To few arguments. (php -f convert_to_po.php localeDir filename.php filename.po' . "\n";
    echo 'Example: php -f convert_to_array.php ../hu_HU/ strings.inc.php messages.po' . "\n";
    return FALSE;
}

$baseDir = realpath($argv[1]);

if (!$baseDir) {
    echo 'Base dir is not exists!';
    return FALSE;
}

$poFilePointer  = $baseDir . '/' . $argv[3];
$phpFilePointer = $baseDir . '/' . $argv[2];

if (!is_file($phpFilePointer)) {
    echo 'Source file is missing!';
    return FALSE;
}

include_once($phpFilePointer);

/**
 * Creating FileObject instances
 */
$poFile = new SplFileObject($poFilePointer, 'w');

if (!isset($strings)) {
    trigger_error('Input file is invalid! Does\'t contains $strings array!');
    return FALSE;
}

/**
 * Simple Header for PO file
 */
$header = <<<HEADER
# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR THE PACKAGE'S COPYRIGHT HOLDER
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
msgid ""
msgstr ""
"Project-Id-Version: PACKAGE VERSION\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: 2012-06-16 09:30+0200\\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"
"Language-Team: LANGUAGE <LL@li.org>\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"

HEADER;


$poFile->fwrite($header);

foreach ($strings as $original => $translated) {
    $poFile->fwrite(__NEWLINE__);
    $poFile->fwrite('msgid "' . str_replace(array('"', "\\'"), array('\\"', "'"), $original) . '"' . __NEWLINE__);
    $poFile->fwrite('msgstr "' . str_replace(array('"', "\\'"), array('\\"', "'"), $translated) . '"' . __NEWLINE__);
}

echo 'Conversion to po is completed...' . "\n";
