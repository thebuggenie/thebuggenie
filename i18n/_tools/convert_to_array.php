<?php

/**
 * Simple PO to Array converter for TBG
 */
define('__NEWLINE__', PHP_EOL);

/**
 * Checking arguments
 */
if ($argc < 5) {
    echo 'To few arguments. (php -f convert_to_array.php localeDir fromLang filename.po toLang filename.php' . "\n";
    echo 'Example: php -f convert_to_array.php ../hu_HU/ en_US messages.po hu_HU strings.inc.php' . "\n";
    return FALSE;
}

$baseDir = realpath($argv[1]);

if (!$baseDir) {
    echo 'Base dir is not exists!';
    return FALSE;
}

$fromFilePointer  = $baseDir . '/' . $argv[3];
$phpFilePointer = $baseDir . '/' . $argv[5];

if(!is_file($fromFilePointer)) {
    echo 'Source file is missing!';
    return FALSE;
}


/**
 * Creating FileObject instances
 */
$fromFile  = new SplFileObject($fromFilePointer, 'r');
$phpFile = new SplFileObject($phpFilePointer, 'w');

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
if($fromFile->getExtension() == "po") {
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
} elseif($fromFile->getExtension() == "ts") {
    
    $xmlReader = new XMLReader();
    $xmlReader->open($fromFilePointer, "utf-8");
    
    $value = '';
    while($xmlReader->read()) {
        
        if($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'name') {
            $category = $xmlReader->readString();
            $current_key = '';
        }
        
        if($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'source') {
            $current_key = str_replace(array('"', '&'), array('\"', "&amp;") ,$xmlReader->readString());
        }
        
        if($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name == 'translation') {
            
            if($xmlReader->getAttribute('type') == 'unfinished') {
                $value = '';
            } else {
                $value = str_replace(array('"', '&'), array('\"', "&amp;") ,$xmlReader->readString());
            }
            
        }
        
        if(!empty($category) && !empty($current_key)) {
            $translations[$category][$current_key] = $value;
        }
    }
    
} else {
    
    echo 'Not valid extension!';
    exit;

}

/**
 * Write translation array to PHP file
 */
$phpFile->fwrite('<?php');
$phpFile->fwrite(__NEWLINE__ . __NEWLINE__);

$phpFile->fwrite("// Number of Sections: " . (count($translations) - 1) . __NEWLINE__);
$phpFile->fwrite("// Updated at: " . date('Y M d.') . __NEWLINE__);
$phpFile->fwrite("// Translator: " . __NEWLINE__);
$phpFile->fwrite("// Translator email: " . __NEWLINE__);

foreach ($translations as $category => $categoryContents) {

    $phpFile->fwrite(__NEWLINE__);
    $phpFile->fwrite('// First occurrence is in: ' . $category . __NEWLINE__);
    $phpFile->fwrite("// ----------------------------------------------------------------------------" . __NEWLINE__);

    foreach ($categoryContents as $msgid => $msgstr) {

        if ($fromLang == $toLang) {
            $msgstr = $msgid;
        }

        if (empty($msgid) && empty($msgstr)) {
            continue;
        }

        if ($msgstr == '') {
            $prefix = '//';
        } else {
            $prefix = '  ';
        }

        if (strpos($msgid, "'") !== FALSE) {
            $phpFile->fwrite($prefix . '$strings["' . $msgid . '"] = "' . $msgstr . '";' . __NEWLINE__);
        } else {
            $phpFile->fwrite($prefix . '$strings[\'' . str_replace(array('\\"', "'"), array('"', "\\'"), $msgid) . '\'] = \'' . str_replace(array('\\"', "'"), array('"', "\\'"), $msgstr) . '\';' . __NEWLINE__);
        }
    }
}

echo 'Conversion to array is completed...' . "\n";
