<?php

class FindJsonTest extends AbstractFindTest {

  function getJsonFile() {
    return __DIR__ . "/" . $this->getDirectory() . "/client.json";
  }

  function getJsonInputFile() {
    return __DIR__ . "/" . $this->getDirectory() . "/custom.json";
  }

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Hello" => "Hello",
      ), $json);
  }

}
