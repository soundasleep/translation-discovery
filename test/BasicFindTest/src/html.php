<p>
  <?php
  $result = array();
  foreach (get_all_cryptocurrencies() as $c) {
    $result[] = "<span class=\"currency_name_" . htmlspecialchars($c) . "\">" . htmlspecialchars(get_currency_name($c)) . " (" . get_currency_abbr($c) . ")</span>" .
      (in_array($c, get_new_supported_currencies()) ? " <span class=\"new\">" . ht("new") . "</span>" : "");
  }
  echo t("Currently :site_name supports the :currencies cryptocurrencies.",
    array(
      ':currencies' => implode_english($result),
    ));
  ?>
</p>