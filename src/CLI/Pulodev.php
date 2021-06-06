<?php

namespace Pulodev\CLI;

/**
 * Scrap APi from https://pulo.dev/
 */
class PuloDev
{
  /**
   * Featch API from polu.dev using curl
   *
   * @param string $url
   *    Api url location
   * @return string|bool
   *    String body api respone, False if respone failed
   */
  public static function getData(string $url)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $respone = curl_exec($ch);
    if ($respone === false) return false;
    curl_close($ch);

    return $respone;
  }

  /**
   * Get api pulodev menggunakn query filter
   *
   * @param string $media
   *    Type media filter (contoh: videp, tulisan, web)
   * @param int $limit
   *    Jumlah data yang akan ditampilkan/dimuat
   * @param string $query
   *    Filter konten bedsarkan kontent
   * @return array
   *    Array hasil featch dari pulo.dev
   */
  public static function filterKontent(string $media = '*', int $limit = 0, string $query = '')
  {
    // TODO: mendukung pagination
    $limit = $limit > 20
      ? 20
      : $limit;

    $query = str_replace(' ', '+', $query);

    $filter = array(
      "page=1",
      "query=$query",
      "media=$media",
    );
    $filter = implode('&', $filter);
    echo 'https://api.pulo.dev/v1/contents?' . $filter . "\n";

    $raw = self::getData('https://api.pulo.dev/v1/contents?' . $filter);
    $json = json_decode($raw, true);

    $data = $json['data'] ?? [];

    return [
      'data'  => array_slice($data, 0, $limit),
      'view'  => $json['total'] ?? 0,
    ] ;
  }
}
