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
    if (function_exists("curl_version")) {
      // menggunakan curl jika mendukung ext-curl
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $respone = curl_exec($ch);
      if ($respone === false) return false;
      curl_close($ch);

      return $respone;
    }

    // menggunakan file get content
    $opts = array(
      'http'=>array(
        'method' => "GET",
        'header' => "Content-Type: application/json\r\n"
      )
    );

    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
  }

  /**
   * Get api pulodev menggunakn query filter
   *
   * @param string $media
   *    Type media filter (contoh: videp, tulisan, web)
   * @param array $option
   *    Option argumnet for catch query, require: limit, query, page and real_page
   * @return array
   *    Array of data
   */
  public static function filterKontent(string $media = '*', array $option)
  {
    // property
    $limit = $option['limit'] ?? 1;
    $query = $option['query'] ?? "";
    $page = $option['page'] ?? 1;
    $real_page = $option['real_page'] ?? 1;

    // senitazer I
    $query = str_replace(' ', '+', $query);
    $limit = $limit < 1
      ? 1
      : $limit;
    $page = $page < 1
      ? 1
      : $page;

    $filter = array(
      "page=$real_page",
      "query=$query",
      "media=$media",
    );
    $filter = implode('&', $filter);
    $url = "https://api.pulo.dev/v1/contents?$filter";

    echo "\e[33m⇲ fetching \e[0m", $url, "\n";

    // featch data
    $raw = self::getData($url);
    $json = json_decode($raw, true);

    // get data
    $data = $json['data'] ?? array();
    $total = $json['total'] ?? 0;

    // handel from error
    if ($data == array() || $total < 0) {
      return array(
        'data'      => $data,
        'view'      => $total,
        'maks_page' => ceil($total / $limit),
      );
    }

    // page info
    $maks_konten = count($data);
    $maks_page = ceil($total / $limit);
    $est_page = ceil(($limit * $page) / $maks_konten);

    // pagination
    $start = ($limit * $page) - $limit;
    $end = $limit > $maks_konten
      ? $maks_konten
      : $limit;

    // jika req view == maks_konten
    if ($limit == $maks_konten && $page > 1) {
      return PuloDev::filterKontent($media, array(
        'limit'     => $limit,
        'query'     => $query,
        'page'      => 0,
        'real_page' => $est_page,
      ));
    }

    // jika req view > maks_konten
    // jika start > mask_koten
    if (($limit * $page) > $maks_konten || $limit > $maks_konten) {
      $kekurangan = ($limit * $page) - $maks_konten;
      $kekurangan = $kekurangan > $limit
        ? $limit
        : $kekurangan;

      // requsing
      $add_data = PuloDev::filterKontent($media, array(
        'limit'     => $kekurangan,
        'query'     => $query,
        'page'      => ($page - ceil($maks_konten/$limit)),
        'real_page' => $real_page + 1,
      ));

      return array(
        'data'      => array_merge(
            array_slice($data, $start, $limit),
            $add_data['data'] ?? array()
          ),
        'view'      => $total,
        'maks_page' => $maks_page,
      );
    }

    // jika req limit < maks_konten
    return array(
      'data'      => array_slice($data, $start, $end),
      'view'      => $total,
      'maks_page' => $maks_page,
    );

  }

}
