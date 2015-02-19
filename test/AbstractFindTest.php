<?php

/**
 * Tests the find script.
 */
abstract class AbstractFindTest extends PHPUnit_Framework_TestCase {

  function getDirectory() {
    return get_class($this);
  }

  function setUp() {
    $this->doFind(__DIR__ . "/" . $this->getDirectory());
  }

  function tearDown() {
    @unlink($this->getJsonFile());
  }

  function getJsonFile() {
    return __DIR__ . "/" . $this->getDirectory() . "/template.json";
  }

  function testTemplateJsonExists() {
    $this->assertTrue(file_exists($this->getJsonFile()), $this->getJsonFile() . " should exist");
  }

  /**
   * Execute the find.php script
   * In the future, this should be done by instantiating a class rather than running shell commands
   */
  function doFind($dir) {
    $command = "php -f " . escapeshellarg(__DIR__ . "/../find.php") . " " . escapeshellarg($dir);
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
