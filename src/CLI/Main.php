<?php

namespace Pulodev\CLI;

class Main extends Command
{
  /**
   * Current Version Application
   */
  const Version = "0.1.2";

  /**
   * Fungsi utama untuk meanggil pulodev cli
   * @param array $args
   *  Argument untuk di parse di terminal ($argv, default array from php cli)
   */
  public function __construct(array $args = [])
  {
    // args 0 polo
    // args 1 option
    // args 2 argument1
    // args 3 argumnrt2
    // args 4 argumnrt3

    $watch_start = microtime(true);
    
    $option = $args[1] ?? '';
    $option = explode(':', $option); 

    // default option
    $limit_view = 1;
    $page = 1;
    $query = '';

    // option 0 action name
    // option 1 action type
   switch ($option[0] ?? 'kontent') {
    case 'konten':
      // get media filter
      $filter_media = $option[1] ?? '*';

      // mancari letak argument
      $query_match =  false;
      $page_match = false;
      for ($i=1; $i < 10; $i++) { 
        $argumnt_switcher = explode('=', $args[$i] ?? '=');
        switch ($argumnt_switcher[0]) {
          case '--query':
            // mengambil nilai pertama
            if (!$query_match) {              
              // get query filter
              $query = $args[$i] ?? '--query=';
              $query = explode('=', $query);
              $query = $query[1] ?? '';
              $query_match = true;
            }
            break;
        }
        
        // get pegination
        if (!$page_match) {
          $is_page = explode('/', $args[$i] ?? '//');
          if (count($is_page) == 2) {
            $limit_view = $is_page[0] ?? 1;
            $page = $is_page[1] ?? 1;
            $page_match = true;
          }          
        }
      }

      // page info
      $real_view = 0;

      // echo curent url
      echo $this->textYellow("\nfetching ");

      // fetch data
      $feeds = PuloDev::filterKontent($filter_media, $limit_view, $query);
      
      // page not found
      if (count($feeds['data']) == 0) {
        echo "\n", $this->paddingLeft(2), $this->textRed("404 tidak ada hasil\n\n");
      }

      // echo feed to console
      foreach ($feeds['data'] as $feed) {
        $real_view++;
        $time = strtotime($feed['original_published_at']);
        echo
          "\n",
          "", $this->textDim($real_view), "",
          $this->paddingLeft(1), ' ', $this->textLightGray($feed['title']), "\n",
          $this->paddingLeft(2), $this->textBlue($feed['owner']), " - ",
            $this->textGreen(date("d-M-Y", $time)), "\n".
          $this->paddingLeft(2), $this->bgBlue(" " . $feed['media'] . " "), ' ',
            $this->textDim($feed['url']),
          "\n";
      }

      // echo information
      echo "\n",
        $this->bgGreen($this->textDim("Info")), "\n",
        "Menampilkan:\t\t", $this->textYellow($real_view), " Article\n",
        "Halaman:\t\t", $this->textYellow($page), "\n",
        "Artikle Tersedia:\t", $this->textYellow($feeds['view']), " " . ucfirst($filter_media), "\n",
        "Durasi:\t\t\t", $this->textYellow(round(microtime(true) - $watch_start, 2)), " Detik\n",
        "\n\n";

      break;
    
    case "--version":
      echo "\nPulo.dev cli version ", Main::Version, "\n\n";
      break;
    case '--help':
      
    default:
      echo
        "\n",
        $this->textBlue("polu.dev"), " in command line write using native ", $this->bgBlue(" php "), "\n",
        "api from - ", $this->textDim("https://pulo.dev/api.html"), "\n",
        "\n",
        "How to use:\n",
          "  ", $this->textYellow("php"), " pulo [flag] \n",
          "  ", $this->textYellow("php"), " pulo [option] ", $this->textDim("[argumnets]"),
        "\n\n",
        "Flags:\n",
          "  ", $this->textDim("--help"), "\t\tMenampilkan Daftar bantuan\n",
          "  ", $this->textDim("--version"), "\t\tMenampilkan versi saat ini\n",
        "\n",
        "Option:\n",
          "  ", $this->textGreen("konten"), "\t\t", "Menampilkan article tanpa filter\n",
          "  ", $this->textGreen("konten"), ":tulisan", "\t", "Menampilkan article dengan filter - tulisan\n",
          "  ", $this->textGreen("konten"), ":web", "\t\t", "Menampilkan article dengan filter - web\n",
          "  ", $this->textGreen("konten"), ":video", "\t\t", "Menampilkan article dengan filter - video\n",
          "  ", $this->textGreen("konten"), ":tool", "\t\t", "Menampilkan article dengan filter - tool\n",
          "  ", $this->textGreen("konten"), ":podcast", "\t", "Menampilkan article dengan filter - podcast\n",
          "  ", $this->textGreen("konten"), ":???", "\t\t", "Menampilkan article dengan filter\n",
        "\n",
        "Argument:\n",
          "  ", "view/page", "\t\t", "view: jumlah data yg akan ditampilkan\n", 
            "\t\t\t", "page: halaman saat ini, ex:2/1\n",
          "  ", $this->textDim("--query=???"), "\t\t", "Menampilkan data dengan memfilter query", 
        "\n\n",
        "Contoh:\n",
          "  ", $this->textYellow("php"), " pulo konten:tulisan 2/1 ", $this->textDim("--query=js"), "\n",
          "  ", $this->textYellow("php"), " pulo konten:tulisan 2/1 ", $this->textDim('--query="start up"'),

        "\n\n"
        ;
        break;    
    }
  }
}
