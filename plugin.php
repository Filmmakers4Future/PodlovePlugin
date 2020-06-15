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
    // Content 
    
    const PODLOVEWEBPLAYER = "[podloveplayer]";

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
    
    // CONSTANTS
    // config 
    const PODLOVE_CONFIG_FEED_URL = "PODLOVE_FEED_URL";
    const PODLOVE_CONFIG_APPLE_PODCAST_ID = "PODLOVE_APPLE_PODCAST_ID";
    const PODLOVE_CONFIG_GOOGLE_PODCAST = "PODLOVE_GOOGLE_PODCAST";
    const PODLOVE_CONFIG_CASTBOX_ID = "PODLOVE_CASTBOX_ID";
    const PODLOVE_CONFIG_POCKET_CASTS = "PODLOVE_POCKET_CASTS";
    const PODLOVE_CONFIG_DEEZER_ID = "PODLOVE_DEEZER_ID";
    const PODLOVE_CONFIG_SOUNDCLOUD_ID = "PODLOVE_SOUNDCLOUD_ID";
    const PODLOVE_CONFIG_SPOTIFY_ID = "PODLOVE_SPOTIFY_ID";
    const PODLOVE_CONFIG_STITCHER_ID = "PODLOVE_STITCHER_ID";
    const PODLOVE_CONFIG_YOUTUBE_ID = "PODLOVE_YOUTUBE_ID";
    const PODLOVE_CONFIG_ACTIVE_TAB = "PODLOVE_ACTIVE_TAB";
    const PODLOVE_CONFIG_EMBEDDING = "PODLOVE_EMBEDDING";
    const PODLOVE_CONFIG_THEME_COLORS = "PODLOVE_THEME_COLORS";
    const PODLOVE_CONFIG_SHARE_CHANNELS = "PODLOVE_SHARE_CHANNELS";
    const PODLOVE_CONFIG_SHARE_PLAYTIME = "PODLOVE_SHARE_PLAYTIME";
    
    // VARIABLES
    
    protected static $PLAYER_BOOTSTRAP_CODE = array(); 

    // HELPER FUNCTIONS
    
    public static function bootstrapPlayer() {
      if (!empty(self::$PLAYER_BOOTSTRAP_CODE)){
        print(fhtml("<!-- Podlove Webplayer Library -->").NL);
        print(fhtml("<script src='".path2uri(__DIR__."/lib/embed.js")."'></script>").NL);
        print(fhtml("<!-- Podlove Webplayer Boostraping -->").NL);
        print(fhtml("<script>"));
        foreach (self::$PLAYER_BOOTSTRAP_CODE as $player) {
          print(fhtml($player).NL);
        }
        print(fhtml("</script>"));
        print(NL);
      }
    }

    protected static function configure() {
      Plugins::preset(static::PODLOVE_CONFIG_FEED_URL, null);
      Plugins::preset(static::PODLOVE_CONFIG_APPLE_PODCAST_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_CASTBOX_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_POCKET_CASTS, null);
      Plugins::preset(static::PODLOVE_CONFIG_DEEZER_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_SOUNDCLOUD_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_SPOTIFY_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_STITCHER_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_YOUTUBE_ID, null);
      Plugins::preset(static::PODLOVE_CONFIG_ACTIVE_TAB, null);
      
      Plugins::preset(static::PODLOVE_CONFIG_EMBEDDING, "/share.html");
      
      Plugins::preset(static::PODLOVE_CONFIG_SHARE_CHANNELS, ["twitter", "facebook", "whats-app", "mail", "link"]);
      Plugins::preset(static::PODLOVE_CONFIG_SHARE_PLAYTIME, true);
      
    }
    
    protected static function getPodlovePlayer($item) {
      // preset plugin configuration
      static::configure();
        
      $result = value($item, CONTENT);

      if (is_string($result)) {
        if (value($item, self::PODLOVE_AUDIOFILES) and value($item, self::PODLOVE_AUDIOMIME) and value($item, self::PODLOVE_AUDIOTITLES) and value($item, self::PODLOVE_AUDIOSIZES)) {
          
          # Base player configuration
          $config = [
            "version" => 5,
            "base" => path2uri(__DIR__)."/lib/",
            "theme" => [
              "tokens" => Plugins::get(static::PODLOVE_CONFIG_THEME_COLORS)
            ],
            "share" => [
              "channels" => Plugins::get(static::PODLOVE_CONFIG_SHARE_CHANNELS),
              "sharePlaytime" => Plugins::get(static::PODLOVE_CONFIG_SHARE_PLAYTIME),
              "outlet" => Plugins::get(static::PODLOVE_CONFIG_EMBEDDING)
            ]
          ];
          
          if(Plugins::get(static::PODLOVE_CONFIG_ACTIVE_TAB)){
            $config["activeTab"] = Plugins::get(static::PODLOVE_CONFIG_ACTIVE_TAB);
          }
          
          # Add subscribe button config
          if(Plugins::get(static::PODLOVE_CONFIG_FEED_URL)){
            $subscribeButton = [
              "feed" => Plugins::get(static::PODLOVE_CONFIG_FEED_URL),
              "clients" => [
                ["id" => "antenna-pod"],
                ["id" => "beyond-pod"],
                ["id" => "clementine"],
                ["id" => "downcast"],
                ["id" => "gpodder"],
                ["id" => "itunes"],
                ["id" => "i-catcher"],
                ["id" => "instacast"],
                ["id" => "overcast"],
                ["id" => "player-fm"],
                ["id" => "pod-grasp"],
                ["id" => "podcast-addict"],
                ["id" => "podcast-republic"],
                ["id" => "podcat"],
                ["id" => "podscout"],
                ["id" => "rss-radio"],
                ["id" => "rss"]
              ]
            ];
            if(Plugins::get(static::PODLOVE_CONFIG_APPLE_PODCAST_ID)){
              $subscribeButton["clients"][] = ["id" => "apple-podcasts", "service" => Plugins::get(static::PODLOVE_CONFIG_APPLE_PODCAST_ID)];
              $subscribeButton["clients"][] = ["id" => "castro", "service" => Plugins::get(static::PODLOVE_CONFIG_APPLE_PODCAST_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_CASTBOX_ID)){
              $subscribeButton["clients"][] = ["id" => "castbox", "service" => Plugins::get(static::PODLOVE_CONFIG_CASTBOX_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_DEEZER_ID)){
              $subscribeButton["clients"][] = ["id" => "deezer", "service" => Plugins::get(static::PODLOVE_CONFIG_DEEZER_ID)];
            } 
            if(Plugins::get(static::PODLOVE_CONFIG_SOUNDCLOUD_ID)){
              $subscribeButton["clients"][] = ["id" => "soundcloud", "service" => Plugins::get(static::PODLOVE_CONFIG_SOUNDCLOUD_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_SPOTIFY_ID)){
              $subscribeButton["clients"][] = ["id" => "spotify", "service" => Plugins::get(static::PODLOVE_CONFIG_SPOTIFY_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_STITCHER_ID)){
              $subscribeButton["clients"][] = ["id" => "stitcher", "service" => Plugins::get(static::PODLOVE_CONFIG_STITCHER_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_YOUTUBE_ID)){
              $subscribeButton["clients"][] = ["id" => "youtube", "service" => Plugins::get(static::PODLOVE_CONFIG_YOUTUBE_ID)];
            }
            if(Plugins::get(static::PODLOVE_CONFIG_GOOGLE_PODCAST)){
              if (Plugins::get(static::PODLOVE_CONFIG_GOOGLE_PODCAST) === True) {
                $subscribeButton["clients"][] = ["id" => "google-podcasts", "service" => Plugins::get(static::PODLOVE_CONFIG_FEED_URL)];
              } else {
                $subscribeButton["clients"][] = ["id" => "google-podcasts", "service" => Plugins::get(static::PODLOVE_CONFIG_GOOGLE_PODCAST)];
              }
            }
            if(Plugins::get(static::PODLOVE_CONFIG_POCKET_CASTS)){
              if (Plugins::get(static::PODLOVE_CONFIG_POCKET_CASTS) === True) {
                $subscribeButton["clients"][] = ["id" => "pocket-casts", "service" => Plugins::get(static::PODLOVE_CONFIG_FEED_URL)];
              } else {
                $subscribeButton["clients"][] = ["id" => "pocket-casts", "service" => Plugins::get(static::PODLOVE_CONFIG_POCKET_CASTS)];
              }
            }
            usort($subscribeButton["clients"], function ($item1, $item2) {
              return $item1['id'] <=> $item2['id'];
            });
            $config["subscribe-button"] = $subscribeButton;
          }
          
          
          // Base episode configuration
          $episode = [
            "version" => 5,
            "show" => [
              "title" => value($item, self::PODLOVE_PODCASTTITLE),
              "link" => value($item, self::PODLOVE_PODCASTLINK)
            ],
            "title" => value($item, self::PODLOVE_TITLE),
            "poster" => value($item, self::PODLOVE_POSTER),
            "duration" => value($item, self::PODLOVE_DURATION),
            "link" => absoluteurl(value($item, URI))
          ];
          
          // Parse audio files from markdown
          $audioFiles = explode(",", value($item, self::PODLOVE_AUDIOFILES));
          $audioSizes = explode(",", value($item, self::PODLOVE_AUDIOSIZES));
          $audioTitles = explode(",", value($item, self::PODLOVE_AUDIOTITLES));
          $audioMimeTypes = explode(",", value($item, self::PODLOVE_AUDIOMIME));
          
          // Parse download files from markdown (optional)
          $downloadFiles = explode(",", value($item, self::PODLOVE_DOWNLOADFILES));
          $downloadSizes = explode(",", value($item, self::PODLOVE_DOWNLOADSIZES));
          $downloadTitles = explode(",", value($item, self::PODLOVE_DOWNLOADTITLES));
          $downloadMimeTypes = explode(",", value($item, self::PODLOVE_DOWNLOADMIME));
          
          // Parse chapters from markdown (optional)
          $chapterTitles = explode(",", value($item, self::PODLOVE_CHAPTERTITLES));
          $chapterStarts = explode(",", value($item, self::PODLOVE_CHAPTERSTARTS));
          $chapterLinks = explode(",", value($item, self::PODLOVE_CHAPTERLINKS));
          $chapterImages = explode(",", value($item, self::PODLOVE_CHAPTERIMAGES));
          
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
          if(value($item, self::PODLOVE_DOWNLOADFILES)){
            foreach($downloadFiles as $i => $downloadFile) {
              $files[] = ["url" => $downloadFile,
                          "size" => $downloadSizes[$i],
                          "title" => $downloadTitles[$i],
                          "mimeType" => $downloadMimeTypes[$i]
                          ];
            };
          }
          
          // Add chapters to chapter array
          if(value($item, self::PODLOVE_CHAPTERTITLES)){
            foreach($chapterTitles as $i => $chapterTitle) {
              $chapters[] = ["title" => $chapterTitle,
                          "start" => $chapterStarts[$i],
                          "href" => $chapterLinks[$i],
                          "image" => $chapterImages[$i]
                          ];
            };
          }
          
          // Add new config arrays to episode array
          $episode["audio"] = $audio;
          $episode["files"] = $files;
          $episode["chapters"] = $chapters;
                 
          # Create random id for podlove webplayer div
          $divID =  "player_".substr(uniqid(rand(),1),0,15);
          
          # Create div for podlove webplayer
          $podloveplayer = fhtml("<div id=\"%s\"></div>".NL,
                               $divID);
                    
          # Add player bootstrap code to array to add after body
          self::$PLAYER_BOOTSTRAP_CODE[] = 'window.podlovePlayer("#'.$divID.'", '.json_encode($episode, JSON_UNESCAPED_SLASHES).', '.json_encode($config, JSON_UNESCAPED_SLASHES).');';
                                
          // replace shortcode with podlove player div
          $result = str_ireplace(static::PODLOVEWEBPLAYER, $podloveplayer, $result);
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
  Plugins::register(PodlovePlayerPlugin::class, "bootstrapPlayer", AFTER_BODY);