<?php

    namespace thebuggenie\core\modules\main\cli;

    use Symfony\Component\Finder\Finder;

    /**
     * CLI command class, main -> extract_strings
     *
     * @author Jean Traullé <jtraulle@gmail.com>
     * @version 2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * CLI command class, main -> extract_strings
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ExtractStrings extends \thebuggenie\core\framework\cli\Command
    {

        private $nbstrings = 0;
        private $nbfiles = 0;
        private $generatedFiles = array();

        protected function _setup()
        {
            $this->_command_name = 'extract_strings';
            $this->_description = "Extract main language (en_US) translatable strings from sources";
            $this->addRequiredArgument('format', "Output file format can be either xliff (XLIFF 1.2) or php (PHP Alternative Array)");
            $this->addOptionalArgument('-v', "Verbose mode");
        }

        protected function do_execute()
        {
            if($this->getProvidedArgument('format') !== 'xliff' && $this->getProvidedArgument('format') !== 'php'){
                $this->cliEcho("\nERROR:", "red", "underline");
                $this->cliEcho(" format parameter can be either xliff or php !\n");
                $this->cliEcho("Example:", "cyan", "underline");
                $this->cliEcho(" ./tbg_cli extract_strings --format=xliff\n\n");
                return;
            }

            $this->extractAndGenerate(THEBUGGENIE_PATH);

            $modules = $this->getModules();

            foreach($modules as $module){
                $this->extractAndGenerate(THEBUGGENIE_PATH . 'modules' . DS . $module, $module);
            }

            $this->displayFinalReport();
        }

        /**
         * @return array Array containing all modules keys
         */
        protected function getModules(){
            $finder = new Finder();
            $finder->directories()
                ->in(THEBUGGENIE_PATH.'/modules')
                ->depth('== 0');

            foreach ($finder as $file) {
                $modules[] = $file->getRelativePathname();
            }

            return $modules;
        }

        /**
         * @param $path Path to search for the source strings
         * @param null $module The module key or null for core
         */
        protected function extractAndGenerate($path, $module = null){

            $module_name = (!isset($module)) ? 'core' : $module;

            if($this->getProvidedArgument(2) != '-v'){
                $this->cliEcho("\nFinding files to process for ");
                $this->cliEcho($module_name, "yellow");
                $this->cliEcho(" module ... ");
            }

            //Finding all .php potentially containing source
            //strings using Symfony2 Finder component.
            $finder = new Finder();
            $finder->files()
                ->in($path)
                ->notpath('/^modules/')
                ->notpath('core/lib')
                ->notpath('core/cache')
                ->notpath('i18n')
                ->notpath('thebuggenie')
                ->notpath('tests')
                ->name('*.php');

            $this->cliEcho("✔ DONE", 'green', 'bold');

            //Array to store our strings
            $allstrings = array();
            $stringsByFile = array();

            //For statistical purposes
            $totalStrings = 0;
            $totalFiles = 0;
            $filesIgnored = 0;
            $filesWithoutStrings = 0;

            if($this->getProvidedArgument(2) != '-v'){
                $this->cliEcho("\n\nProcessing files for ");
                $this->cliEcho($module_name." ", "yellow");
                $this->cliEcho(" module ");
            }

            foreach ($finder as $file) {
                $filename = $file->getRelativePathname();
                $file = file_get_contents($file->getRealpath());

                //Regular expression that match __() pattern
                preg_match_all("/__\(('|\")(.*?[^\\\\])(?:\\1)/", $file, $strings);
                $strings = str_replace("\\", "", $strings[2]);

                if ($this->getProvidedArgument(2) == '-v') {
                    $this->cliEcho("Processing file ");
                    $this->cliEcho($filename."\n", 'yellow');
                    $this->cliEcho(" ↳  ");
                }

                if(!empty($strings)){
                    $stringsWithoutDuplicates = array_values(array_unique($strings));
                    $stringsWithoutDuplicatesFromAllStringsTab = array_diff($stringsWithoutDuplicates, $allstrings);

                    $numberOfStringsInFile = count($stringsWithoutDuplicatesFromAllStringsTab);

                    if($numberOfStringsInFile){
                        if ($this->getProvidedArgument(2) == '-v'){
                            $this->cliEcho($numberOfStringsInFile." ", 'green', 'bold');
                            $this->cliEcho(($numberOfStringsInFile > 1) ? "strings found\n" : "string found\n");
                        }else{
                            $this->cliEcho(".");
                        }

                        $totalStrings += $numberOfStringsInFile;
                        $this->nbstrings += $numberOfStringsInFile;
                        $totalFiles++;
                        $this->nbfiles++;

                        $allstrings = array_merge($allstrings, $stringsWithoutDuplicatesFromAllStringsTab);
                        $stringsByFile[$filename] = array_values($stringsWithoutDuplicatesFromAllStringsTab);
                    }else{
                        if ($this->getProvidedArgument(2) == '-v')
                            $this->cliEcho("File ignored since found strings were already in previous processed files.\n", 'cyan');
                        $filesIgnored++;
                    }
                }else{
                    if ($this->getProvidedArgument(2) == '-v')
                        $this->cliEcho("There were no string in the file.\n", 'cyan');
                    $filesWithoutStrings++;
                }

                if ($this->getProvidedArgument(2) == '-v') $this->cliEcho("\n");
            }

            if($this->getProvidedArgument(2) != '-v'){
                $this->cliEcho(" ✔ DONE\n\n", 'green', 'bold');
            }

            if($totalStrings > 0){
                //Displaying extraction report to user
                $this->cliEcho("Extraction of ");
                $this->cliEcho("en_US", "yellow");
                $this->cliEcho(" source strings for module ");
                $this->cliEcho($module_name." ", "yellow");
                $this->cliEcho("✔ COMPLETED\n", 'green', 'bold');

                $this->cliEcho(" ↳  ");
                $this->cliEcho($totalStrings, 'green', 'bold');
                $this->cliEcho(" strings found\n");

                $this->cliEcho(" ↳  through ");
                $this->cliEcho($totalFiles, 'green', 'bold');
                $this->cliEcho(" files\n");

                $this->cliEcho(" ↳  ");
                $this->cliEcho($filesIgnored, 'cyan', 'bold');
                $this->cliEcho(" files has been ignored (all found strings were already in previous files)\n");

                $this->cliEcho(" ↳  ");
                $this->cliEcho($filesWithoutStrings, 'cyan', 'bold');
                $this->cliEcho(" files did not contain any string\n");

                $this->cliEcho("\n");

                $this->cliEcho("Generating resulting ");

                switch($this->getProvidedArgument('format')){
                    case "xliff":
                        $this->cliEcho($module.".xlf", "yellow");
                        $relative_path = $this->writeFileOnDisk($this->generateXliff($totalFiles, $totalStrings, $stringsByFile), $module, 'xlf');
                        break;
                    case "php":
                        $this->cliEcho($module.".inc.php", "yellow");
                        $relative_path = $this->writeFileOnDisk($this->generatePhpAltArray($totalFiles, $totalStrings, $stringsByFile), $module, 'inc.php');
                        break;
                }

                $this->cliEcho(" file ...\n");

                $this->cliEcho(" ↳  ");
                $this->cliEcho($relative_path, 'magenta', 'bold');
                $this->cliEcho(" ✔ GENERATED\n\n", 'green', 'bold');
            }
        }

        /**
         * @param $totalFiles int Number of files (sections) for the file to generate
         * @param $totalStrings int Number of strings (in all sections) for the file to generate
         * @param $stringsByFile array Associative array containing strings ordered by files
         * @return string
         */
        protected function generateXliff($totalFiles, $totalStrings, $stringsByFile){
            $generatedFile = '<?xml version="1.0"?>'.PHP_EOL.PHP_EOL;

            $generatedFile .= "<!-- Please, do not edit this file ! -->".PHP_EOL;
            $generatedFile .= "<!-- If you would like to help translating TBG, -->".PHP_EOL;
            $generatedFile .= "<!-- please visit https://www.transifex.com/projects/p/tbg -->".PHP_EOL.PHP_EOL;

            $generatedFile .= "<!-- Number of Sections: $totalFiles -->".PHP_EOL;
            $generatedFile .= "<!-- Number of Strings: $totalStrings -->".PHP_EOL;
            $generatedFile .= "<!-- Keys extracted from sources on: ".date('Y M d.')." -->".PHP_EOL;
            $generatedFile .= "<!-- Translations extracted from Transifex on: -->".PHP_EOL.PHP_EOL;

            $generatedFile .= '<xliff version="1.2">'.PHP_EOL;

            foreach ($stringsByFile as $file => $strings) {

                $generatedFile .= PHP_EOL;
                $generatedFile .= '    <file source-language="en" datatype="php" original="'.$file.'">'.PHP_EOL;
                $generatedFile .= "        <body>".PHP_EOL;

                foreach ($strings as $string) {
                    $string = trim($string);

                    $generatedFile .= '            <trans-unit id="'.md5($string).'">'.PHP_EOL;
                    $generatedFile .= '                <source>'.htmlspecialchars($string,ENT_NOQUOTES).'</source>'.PHP_EOL;
                    $generatedFile .= '            </trans-unit>'.PHP_EOL;
                }

                $generatedFile .= '        </body>'.PHP_EOL;
                $generatedFile .= "    </file>".PHP_EOL;
            }

            $generatedFile .= "</xliff>";

            return $generatedFile;
        }

        /**
         * @param $totalFiles int Number of files (sections) for the file to generate
         * @param $totalStrings int Number of strings (in all sections) for the file to generate
         * @param $stringsByFile array Associative array containing strings ordered by files
         * @return string
         */
        protected function generatePhpAltArray($totalFiles, $totalStrings, $stringsByFile){
            $generatedFile = "<?php".PHP_EOL.PHP_EOL;

            $generatedFile .= "// Please, do not edit this file !".PHP_EOL;
            $generatedFile .= "// If you would like to help translating TBG,".PHP_EOL;
            $generatedFile .= "// please visit https://www.transifex.com/projects/p/tbg".PHP_EOL.PHP_EOL;

            $generatedFile .= "// Number of Sections: $totalFiles".PHP_EOL;
            $generatedFile .= "// Number of Strings: $totalStrings".PHP_EOL;
            $generatedFile .= "// Keys extracted from sources on: ".date('Y M d.').PHP_EOL;
            $generatedFile .= "// Translations extracted from Transifex on: ".PHP_EOL;

            foreach ($stringsByFile as $file => $strings) {

                $generatedFile .= PHP_EOL;
                $generatedFile .= "// First occurrence is in: ".$file.PHP_EOL;
                $generatedFile .= "// ----------------------------------------------------------------------------".PHP_EOL;

                foreach ($strings as $string) {
                    $string = trim($string);

                    if (strpos($string, "'") !== FALSE)
                        $generatedFile .= '  $strings["' . str_replace('"', '\\"', $string) . '"] = "' . str_replace('"', '\\"', $string) . '";' . PHP_EOL;
                    else
                        $generatedFile .= '  $strings[\'' . str_replace("'", "\\'", $string) . '\'] = \'' . str_replace("'", "\\'", $string) . '\';' . PHP_EOL;
                }
            }

            return $generatedFile;
        }

        /**
         * @param $generatedFile string Text content of generated file to write
         * @param $ext string Module key
         * @param $ext string Extension of file
         * @return array Write language file on disk
         */
        protected function writeFileOnDisk($generatedFile, $module, $ext){
            if(!isset($module)){
                $relative_path = 'i18n' . DS . 'en_US' . DS . 'strings.' . $ext;
                file_put_contents(THEBUGGENIE_PATH . $relative_path, $generatedFile);
                $this->generatedFiles[] = $relative_path;
            }else{
                $relative_path = 'modules' . DS . $module . DS . 'i18n' . DS . 'en_US';
                if(!file_exists($relative_path))
                    mkdir(THEBUGGENIE_PATH . $relative_path, 0755, true);

                $relative_path .= DS . $module . '.' . $ext;
                file_put_contents(THEBUGGENIE_PATH . $relative_path, $generatedFile);
                $this->generatedFiles[] = $relative_path;
            }

            return $relative_path;
        }

        /**
         * Print out final statistical report about extraction and generation process
         */
        protected function displayFinalReport(){
            $this->cliEcho("\n\n*********************************\n");
            $this->cliEcho("*   END OF EXTRACTION PROCESS   *\n");
            $this->cliEcho("*********************************\n\n");

            $this->cliEcho("Extraction of ");
            $this->cliEcho("en_US", "yellow");
            $this->cliEcho(" source strings for core + ");
            $this->cliEcho(count($this->getModules()), "yellow");
            $this->cliEcho(" modules ");
            $this->cliEcho("✔ COMPLETED\n", 'green', 'bold');

            $this->cliEcho(" ↳  ");
            $this->cliEcho($this->nbstrings, 'green', 'bold');
            $this->cliEcho(" strings found\n");

            $this->cliEcho(" ↳  through ");
            $this->cliEcho($this->nbfiles, 'green', 'bold');
            $this->cliEcho(" files\n\n");

            $this->cliEcho(count($this->getModules())+1, "yellow");
            $this->cliEcho(" files were generated :\n");
            foreach($this->generatedFiles as $filepath){
                $this->cliEcho(" ↳  ");
                $this->cliEcho($filepath."\n", 'cyan');
            }

            $this->cliEcho("\n");
        }
    }
