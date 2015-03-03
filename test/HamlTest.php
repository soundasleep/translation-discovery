<?php

/**
 * Template finding in HAML templates
 */
class HamlTest extends AbstractFindTest {

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Hello" => "Hello",
        "World" => "World",
        "book" => "book",
        "books" => "books",
        "cat" => "cat",
        "cats" => "cats",
      ), $json);
  }

}
