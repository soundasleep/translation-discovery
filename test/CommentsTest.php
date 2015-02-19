<?php

/**
 * Test that we can ignore strings in comments.
 */
class CommentsTest extends AbstractFindTest {

  function testJson() {
    $json = json_decode(file_get_contents($this->getJsonFile()), true /* assoc */);

    $this->assertEquals(array(
        "Cat" => "Cat",
        "Hello" => "Hello",
        "Visit this group" => "Visit this group",
      ), $json);
  }

}
