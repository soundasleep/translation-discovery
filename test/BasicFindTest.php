<?php

/**
 * Basic template finding
 */
class BasicFindTest extends AbstractFindTest {

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Currently :site_name supports the :currencies cryptocurrencies." => "Currently :site_name supports the :currencies cryptocurrencies.",
        "Hello" => "Hello",
        "Hello, world!" => "Hello, world!",
        "World" => "World",
        "book" => "book",
        "books" => "books",
        "new" => "new",
      ), $json);
  }

}
