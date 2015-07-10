<?php
class URLUtils
{
  // workaround for file_get_contents being disabled by some web hosts
  public static function get($URL)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) {
      return $contents;
    } else {
      return FALSE;
    }
  }

  // check if a url exists
  public static function exists($url)
  {
    $handle   = curl_init($url);
    if (false === $handle) {
      return false;
    }

    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") );
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);

    return $connectable;
  }
}
