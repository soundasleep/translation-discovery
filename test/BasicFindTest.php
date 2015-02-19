<?php

/**
 * Basic template finding
 */
class BasicFindTest extends AbstractFindTest {

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Hello" => "Hello",
        "World" => "World",
      ), $json);
  }

}
