<?php
namespace CrazyFactory\CS\Tests;

use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Util\Standards;

class AllSniffs extends \PHP_CodeSniffer\Tests\Standards\AllSniffs
{
    public static function suite()
    {
        $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']   = array();
        $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = array();

        $suite = new \PHPUnit_Framework_TestSuite('PHP CodeSniffer Standards');

        $isInstalled = !is_file(__DIR__.'/../../autoload.php');

        // Optionally allow for ignoring the tests for one or more standards.
        $ignoreTestsForStandards = getenv('PHPCS_IGNORE_TESTS');
        if ($ignoreTestsForStandards === false) {
            $ignoreTestsForStandards = array();
        } else {
            $ignoreTestsForStandards = explode(',', $ignoreTestsForStandards);
        }

        $installedPaths = [__DIR__.'/../src'];
        Config::setConfigData('installed_paths', __DIR__.'/../src');
        //        $installedPaths = Standards::getInstalledStandardPaths();

        foreach ($installedPaths as $path) {
            $standards = Standards::getInstalledStandards(true, $path);

            // If the test is running PEAR installed, the built-in standards
            // are split into different directories; one for the sniffs and
            // a different file system location for tests.
            if ($isInstalled === true && is_dir($path.DIRECTORY_SEPARATOR.'Generic') === true) {
                $testPath = realpath(__DIR__.'/../../src/Standards');
            } else {
                $testPath = $path;
            }

            foreach ($standards as $standard) {
                if (in_array($standard, $ignoreTestsForStandards) === true) {
                    continue;
                }

                $standardDir = $path.DIRECTORY_SEPARATOR.$standard;
                $testsDir    = $testPath.DIRECTORY_SEPARATOR.$standard.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;

                if (is_dir($testsDir) === false) {
                    // Check if the installed path is actually a standard itself.
                    $standardDir = $path;
                    $testsDir    = $testPath.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR;
                    if (is_dir($testsDir) === false) {
                        // No tests for this standard.
                        continue;
                    }
                }

                $di = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testsDir));

                foreach ($di as $file) {
                    // Skip hidden files.
                    if (substr($file->getFilename(), 0, 1) === '.') {
                        continue;
                    }

                    // Tests must have the extension 'php'.
                    $parts = explode('.', $file);
                    $ext   = array_pop($parts);
                    if ($ext !== 'php') {
                        continue;
                    }

                    $className = Autoload::loadFile($file->getPathname());
                    $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$className] = $standardDir;
                    $GLOBALS['PHP_CODESNIFFER_TEST_DIRS'][$className]     = $testsDir;
                    $suite->addTestSuite($className);
                }
            }//end foreach
        }//end foreach

        return $suite;

    }
}