<?php
  $row = 1;
  if (($handle = fopen("Ultraviolet.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row != 7) {
        $num = count($data);
              echo "<p> $num fields in line $row: <br /></p>\n";
              $row++;
              echo "UVI = " . $data[1] . "<br />\n";
              echo "ipdateTime = " . $data[6] . "<br />\n";
          }
          fclose($handle);
  }
?>