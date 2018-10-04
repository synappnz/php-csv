# php-csv

include "php-csv";

$csv = new csv(file_get_contents("filename.csv"));
$rows = $csv->rows();
foreach ($rows as $row)
{
  // do something with $row
}
