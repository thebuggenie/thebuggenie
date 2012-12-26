<?php

/**
 * Simple PO to TS converter for TBG
 */
define('__NEWLINE__', PHP_EOL);

/**
 * Checking arguments
 */
if ($argc < 5) {
    echo 'To few arguments. (php -f convert_to_array.php localeDir fromLang filename.po toLang filename.ts' . "\n";
    echo 'Example: php -f convert_to_ts.php ../hu_HU/ en_US messages.po hu_HU messages.ts' . "\n";
    return FALSE;
}

$baseDir = realpath($argv[1]);

if (!$baseDir) {
    echo 'Base dir is not exists!';
    return FALSE;
}

$fromFilePointer  = $baseDir . '/' . $argv[3];
$tsFilePointer = $baseDir . '/' . $argv[5];

if(!is_file($fromFilePointer)) {
    echo 'Source file is missing!';
    return FALSE;
}


/**
 * Creating FileObject instances
 */
$fromFile  = new SplFileObject($fromFilePointer, 'r');
$tsFile = new XMLWriter();

/**
 * Setting up Languages
 */
$fromLang = $argv[2];
$toLang   = $argv[4];

/**
 * Initializing general variables
 */
$previousLine = NULL;
$current_key  = NULL;
$category     = '';
$translations = array();

/**
 * Reading PO file and generates translation array
 */
foreach ($fromFile as $lineNumber => $line) {

    if ($lineNumber > 0 && $previousLine == __NEWLINE__ && mb_substr($line, 0, 2) == '#:') {
        $category = trim(mb_substr(mb_substr($line, 0, mb_strpos($line, ':', 3)), 2));

        if(mb_substr($category, 0, 1) == '.') {
            $category = mb_substr($category, 1);
        }

        $category = str_replace(array('\\'), array('/'), $category);
    }

    if (mb_substr($line, 0, 5) == 'msgid') {
        $current_key = trim(mb_substr(trim(mb_substr($line, 5)), 1, -1));
    }

    if (!empty($category) && mb_substr($line, 0, 6) == 'msgstr') {
        $translations[$category][$current_key] = trim(mb_substr(trim(mb_substr($line, 6)), 1, -1));
    }

    $previousLine = $line;
}

/**
 * Write translation PO to TS file
 */

$tsFile->openUri($tsFilePointer);
$tsFile->setIndent(4);
$tsFile->setIndentString(' ');

$tsFile->startDocument('1.0', 'utf-8');
    $tsFile->writeRaw('<!DOCTYPE TS>'."\n");

    $tsFile->startElement('TS');
        $tsFile->writeAttribute('version', '2.0');
        $tsFile->writeAttribute('language', $toLang);
        $tsFile->writeAttribute('sourcelanguage', $fromLang);

        foreach ($translations as $category => $categoryContents) {

            $tsFile->startElement('context');
                
                $tsFile->writeElement('name', $category);

                foreach ($categoryContents as $msgid => $msgstr) {

                    if ($fromLang == $toLang) {
                        $msgstr = $msgid;
                    }

                    if (empty($msgid) && empty($msgstr)) {
                        continue;
                    }

                    $msgid = str_replace(array('\"', '\'', "&amp;", "&ndash;", "&gt;", "&lt;"), array('"', "'", '&', '–', '>', '<'), $msgid);
                    $msgstr = str_replace(array('\"', '\'', "&amp;", "&ndash;", "&gt;", "&lt;"), array('"', "'", '&', '–', '>', '<'), $msgstr);
                      
                    $msgstr = html_entity_decode($msgstr, ENT_XHTML, "UTF-8");
                    
                    $tsFile->startElement('message');
                        $tsFile->writeElement('source', $msgid);
                        $tsFile->startElement('translation');

                            if($msgstr == '') {
                                $tsFile->writeAttribute('type', 'unfinished');
                            }
                            
                            $tsFile->text($msgstr);
                            
                        $tsFile->endElement();
                    $tsFile->endElement();
                }

            $tsFile->endElement();
        }

    $tsFile->endElement();
$tsFile->endDocument();

echo 'Conversion to TS is completed...' . "\n";
