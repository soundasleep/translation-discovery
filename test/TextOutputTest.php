<?php

class TextOutputTest extends AbstractFindTest {

  function getTextFile() {
    return str_replace(".json", ".txt", $this->getJsonFile());
  }

  function tearDown() {
    parent::tearDown();
    @unlink($this->getTextFile());
  }

  function testTest() {
    $text = file_get_contents($this->getTextFile());

    $this->assertEquals("Hello\nHello, <person>!\nWorld\n", $text);
  }

  function testTemplateTextExists() {
    $this->assertTrue(file_exists($this->getTextFile()), $this->getTextFile() . " should exist");
  }

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Hello" => "Hello",
        "Hello, :person!" => "Hello, :person!",
        "World" => "World",
      ), $json);
  }

}
