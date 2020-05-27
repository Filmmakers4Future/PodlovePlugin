<?php

  /**
    This is the PodlovePlugin plugin.

    This file contains the PodlovePlugin plugin. 

    @package Filmmakers4Future/PodlovePlugin
    @version 0.2
    @author  Paul-Vincent Roll <paul-vincent@filmmakersforfuture.org>
    @since   0.2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class PodlovePlayerPlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const PODLOVE_AUDIOFILES = "podlove_audioFiles";
    const PODLOVE_PODCASTTITLE = "podlove_podcastTitle";
    const PODLOVE_PODCASTLINK = "podlove_podcastLink";
    
    const PODLOVE_TITLE = "podlove_title";
    const PODLOVE_POSTER = "podlove_poster";
    const PODLOVE_DURATION = "podlove_duration";
    const PODLOVE_AUDIOSIZES = "podlove_audioSizes";
    const PODLOVE_AUDIOTITLES = "podlove_audioTitles";
    const PODLOVE_AUDIOMIME = "podlove_audioMimeTypes";
    
    const PODLOVE_DOWNLOADFILES = "podlove_downloadFiles";
    const PODLOVE_DOWNLOADSIZES = "podlove_downloadSizes";
    const PODLOVE_DOWNLOADTITLES = "podlove_downloadTitles";
    const PODLOVE_DOWNLOADMIME = "podlove_downloadMimeTypes";
    
    const PODLOVE_CHAPTERTITLES = "podlove_chapterTitles";
    const PODLOVE_CHAPTERSTARTS= "podlove_chapterStarts";
    const PODLOVE_CHAPTERLINKS = "podlove_chapterLinks";
    const PODLOVE_CHAPTERIMAGES = "podlove_chapterImages";

    // HELPER FUNCTIONS

    protected static function getPodlovePlayer($item) {
      $result = value($item, CONTENT);

      if (is_string($result)) {
        if (value($item, PODLOVE_AUDIOFILES)) {
          
          // Base episode configuration
          $episode = [
            "version" => "5",
            "show" => [
              "title" => value($item, PODLOVE_PODCASTTITLE),
              "link" => value($item, PODLOVE_PODCASTLINK)
            ],
            "title" => value($item, PODLOVE_TITLE),
            "poster" => value($item, PODLOVE_POSTER),
            "duration" => value($item, PODLOVE_DURATION),
            "link" => absoluteurl(value($item, URI))
          ];
          
          // Parse audio files from markdown
          $audioFiles = explode(",", value($item, PODLOVE_AUDIOFILES));
          $audioSizes = explode(",", value($item, PODLOVE_AUDIOSIZES));
          $audioTitles = explode(",", value($item, PODLOVE_AUDIOTITLES));
          $audioMimeTypes = explode(",", value($item, PODLOVE_AUDIOMIME));
          
          // Parse download files from markdown (optional)
          $downloadFiles = explode(",", value($item, PODLOVE_DOWNLOADFILES));
          $downloadSizes = explode(",", value($item, PODLOVE_DOWNLOADSIZES));
          $downloadTitles = explode(",", value($item, PODLOVE_DOWNLOADTITLES));
          $downloadMimeTypes = explode(",", value($item, PODLOVE_DOWNLOADMIME));
          
          // Parse chapters from markdown (optional)
          $chapterTitles = explode(",", value($item, PODLOVE_CHAPTERTITLES));
          $chapterStarts = explode(",", value($item, PODLOVE_CHAPTERSTARTS));
          $chapterLinks = explode(",", value($item, PODLOVE_CHAPTERLINKS));
          $chapterImages = explode(",", value($item, PODLOVE_CHAPTERIMAGES));
          
          // Create arrays for data
          $audio = array();
          $files = array();
          $chapters = array();
          
          // Add audio files to audio array
          foreach($audioFiles as $i => $audioFile) {
            $audio[] = ["url" => $audioFile,
                        "size" => $audioSizes[$i],
                        "title" => $audioTitles[$i],
                        "mimeType" => $audioMimeTypes[$i]
                        ];
            // Also add the audio files to the download list
            $files[] = ["url" => $audioFile,
                        "size" => $audioSizes[$i],
                        "title" => "Episode: ".$audioTitles[$i],
                        "mimeType" => $audioMimeTypes[$i]
                        ];
          };
          
          // Add download files to files array
          foreach($downloadFiles as $i => $downloadFile) {
            $files[] = ["url" => $downloadFile,
                        "size" => $downloadSizes[$i],
                        "title" => $downloadTitles[$i],
                        "mimeType" => $downloadMimeTypes[$i]
                        ];
          };
          
          // Add chapters to chapter array
          foreach($chapterTitles as $i => $chapterTitle) {
            $chapters[] = ["title" => $chapterTitle,
                        "start" => $chapterStarts[$i],
                        "href" => $chapterLinks[$i],
                        "image" => $chapterImages[$i]
                        ];
          };
          
          // Add new config arrays to episode array
          $episode["audio"] = $audio;
          $episode["files"] = $files;
          $episode["chapters"] = $chapters;
            
          // Load config json from file and parse it
          $configJSON = file_get_contents(__DIR__."/defaults/config.json");
          $configArray = json_decode($configJSON, true);
                 
          # Create random id for podlove webplayer div
          $divID =  "player_".substr(uniqid(rand(),1),0,15);
          
          # Create div for podlove webplayer
          $podloveplayer = fhtml("<div id=\"%s\" class=\"align-content-center\"></div>".NL,
                               $divID);

          # Add script to load podlove webplayer with our configuration                  
          $podloveplayer .= fhtml("<script>");
          // Better way to do this? Could not use fhtml since it invalidates the json data
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
