<?php

  /**
    This is the PodlovePlayerPlugin plugin.

    This file contains the PodlovePlayerPlugin plugin. 

    @package urlaube\PodlovePlayerPlugin
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class PodlovePlayerPlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    // todo 

    // HELPER FUNCTIONS

    protected static function getPodlovePlayer($item) {
      $result = value($item, CONTENT);

      if (is_string($result)) {
        // we at least need the audio file
        if (value($item, "podlove_audioFiles")) {
          $episode = [
            "version" => "5",
            "show" => [
              "title" => value($item, "podlove_podcastTitle"),
              "link" => value($item, "podlove_podcastLink")
            ],
            "title" => value($item, "podlove_title"),
            "poster" => value($item, "podlove_poster"),
            "duration" => value($item, "podlove_duration"),
            "link" => value($item, "podlove_link")
          ];
          
          $audioFiles = explode(",", value($item, "podlove_audioFiles"));
          $audioSizes = explode(",", value($item, "podlove_audioSizes"));
          $audioTitles = explode(",", value($item, "podlove_audioTitles"));
          $audioMimeTypes = explode(",", value($item, "podlove_audioMimeTypes"));
          
          $downloadFiles = explode(",", value($item, "podlove_downloadFiles"));
          $downloadSizes = explode(",", value($item, "podlove_downloadSizes"));
          $downloadTitles = explode(",", value($item, "podlove_downloadTitles"));
          $downloadMimeTypes = explode(",", value($item, "podlove_downloadMimeTypes"));
          
          $chapterTitles = explode(",", value($item, "podlove_chapterTitles"));
          $chapterStarts = explode(",", value($item, "podlove_chapterStarts"));
          $chapterLinks = explode(",", value($item, "podlove_chapterLinks"));
          $chapterImages = explode(",", value($item, "podlove_chapterImages"));
          
          $audio = array();
          $files = array();
          $chapters = array();
          
          foreach($audioFiles as $i => $audioFile) {
            $audio[] = ["url" => $audioFile,
                        "size" => $audioSizes[$i],
                        "title" => $audioTitles[$i],
                        "mimeType" => $audioMimeTypes[$i]
                        ];
            $files[] = ["url" => $audioFile,
                        "size" => $audioSizes[$i],
                        "title" => "Episode: ".$audioTitles[$i],
                        "mimeType" => $audioMimeTypes[$i]
                        ];
          };
          
          foreach($downloadFiles as $i => $downloadFile) {
            $files[] = ["url" => $downloadFile,
                        "size" => $downloadSizes[$i],
                        "title" => $downloadTitles[$i],
                        "mimeType" => $downloadMimeTypes[$i]
                        ];
          };
          
          foreach($chapterTitles as $i => $chapterTitle) {
            $chapters[] = ["title" => $chapterTitle,
                        "start" => $chapterStarts[$i],
                        "href" => $chapterLinks[$i],
                        "image" => $chapterImages[$i]
                        ];
          };
          
          $episode["audio"] = $audio;
          $episode["files"] = $files;
          $episode["chapters"] = $chapters;
            
          $configJSON = file_get_contents(__DIR__."/defaults/config.json");
          $configArray = json_decode($configJSON, true);
                 
          $divID =  "player_".substr(uniqid(rand(),1),0,15);
          
          $podloveplayer = fhtml("<div id=\"%s\" class=\"align-content-center\"></div>".NL,
                               $divID);

          $podloveplayer .= fhtml("<script>");
          $podloveplayer .= 'window.addEventListener("load",function(){window.podlovePlayer("#'.$divID.'", '.json_encode($episode).', '.json_encode($configArray).')},false);';
          $podloveplayer .= fhtml("</script>");
                                
          // replace shortcode with podlove player
          $result = str_ireplace("[podloveplayer]", $podloveplayer, $result);
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function plugin($content) {
      $result = $content;

      if ($result instanceof Content) {
        if ($result->isset(CONTENT)) {
          $result->set(CONTENT, static::getPodlovePlayer($result));
        }
      } else {
        if (is_array($result)) {
          // iterate through all content items
          foreach ($result as $result_item) {
            if ($result_item instanceof Content) {
              if ($result_item->isset(CONTENT)) {
                $result_item->set(CONTENT, static::getPodlovePlayer($result_item));
              }
            }
          }
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(PodlovePlayerPlugin::class, "plugin", FILTER_CONTENT);
