<?php

/**
 * Tests the find script.
 */
abstract class AbstractFindTest extends PHPUnit_Framework_TestCase {

  function getDirectory() {
    return get_class($this);
  }

  function setUp() {
    $this->doFind(__DIR__ . "/" . $this->getDirectory(), $this->getJsonInputFile());
  }

  function tearDown() {
    @unlink($this->getJsonFile());
  }

  function getJsonFile() {
    return __DIR__ . "/" . $this->getDirectory() . "/template.json";
  }

  function getJsonInputFile() {
    return false;   // default
  }

  function testTemplateJsonExists() {
    $this->assertTrue(file_exists($this->getJsonFile()), $this->getJsonFile() . " should exist");
  }

  /**
   * Execute the find.php script
   * In the future, this should be done by instantiating a class rather than running shell commands
   * @param $json optional JSON input file for the find script
   */
  function doFind($dir, $json = false) {
    $command = "php -f " . escapeshellarg(__DIR__ . "/../find.php") . " " . escapeshellarg($dir);
    if ($json) {
      $command .= " " . escapeshellarg($json);
    }
    if ($this->isDebug()) {
      echo ">>> $command\n";
      $last = system($command, $return);
    } else {
      $last = exec($command, $ignored, $return);
    }
    $this->assertEquals(0, $return, "Find script returned $return: '$last'");
  }

  function isDebug() {
    global $argv;
    if (isset($argv)) {
      foreach ($argv as $value) {
        if ($value === "--debug" || $value === "--verbose") {
          return true;
        }
      }
    }
    return false;
  }

}
