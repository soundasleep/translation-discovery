<?php

class RecursiveTest extends AbstractFindTest {

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Cat" => "Cat",
        "Hello" => "Hello",
        "World" => "World",
      ), $json);
  }

}
