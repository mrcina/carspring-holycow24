
<?php

// https://www.carspring.co.uk/rss-feed

$file = new SplFileObject("https://www.carspring.co.uk/rss-feed");
if (!$file->eof()) {
     $file->seek($lineNumber);
     $contents = $file->current(); // $contents would hold the data from line x
}
?>