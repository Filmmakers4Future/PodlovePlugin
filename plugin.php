<?php

  /**
    This is the PodcastAudioPlugin plugin.

    This file contains the PodcastAudioPlugin plugin. It provides an audio player
    feature and a shortcode for the display of the audio player.

    @package urlaube\podcastaudioplugin
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class PodcastAudioPlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const PODCAST_IMAGE = "podcast_image";

    const AUDIOIMAGE    = "audioimage";
    const ENCLOSURE     = "enclosure";
    const ENCLOSURETYPE = "enclosuretype";
    const IMAGE         = "image";

    // HELPER FUNCTIONS

    protected static function getAudioPlayer($item) {
      $result = value($item, CONTENT);

      if (is_string($result)) {
        // retrieve the audio file
        $audiofile = value($item, self::ENCLOSURE);
        $audiotype = value($item, self::ENCLOSURETYPE);

        // retrieve the background image
        $background = value($item, self::AUDIOIMAGE);
        if (null === $background) {
          $background = value($item, self::IMAGE);
        }
        if (null === $background) {
          $background = value(Handlers::class, self::PODCAST_IMAGE);
        }

        // we at least need the audio file
        if (null !== $audiofile) {
          // generate audio player source code
          $audioplayer = fhtml("<link href=\"%s\" rel=\"stylesheet\">".NL,
                               path2uri(__DIR__."/css/style.css"));

          // insert the background image
          if (null !== $background) {
            $audioplayer .= fhtml("<div class=\"podcastaudio podcastaudio-image\">".NL.
                                  "  <img src=\"%s\">".NL,
                                  $background);
          } else {
            $audioplayer .= fhtml("<div class=\"podcastaudio\">".NL);
          }

          // insert the audio file
          if (null !== $audiotype) {
            $audioplayer .= fhtml("  <audio controls preload=\"metadata\">".NL.
                                  "    <source src=\"%s\" type=\"%s\">".NL.
                                  "  </audio>".NL.
                                  "</div>",
                                  $audiofile,
                                  $audiotype);
          } else {
            $audioplayer .= fhtml("  <audio controls preload=\"metadata\">".NL.
                                  "    <source src=\"%s\">".NL.
                                  "  </audio>".NL.
                                  "</div>",
                                  $audiofile);
          }

          // replace shortcode with audio player
          $result = str_ireplace("[podcastaudio]", $audioplayer, $result);
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function plugin($content) {
      $result = $content;

      if ($result instanceof Content) {
        if ($result->isset(CONTENT)) {
          $result->set(CONTENT, static::getAudioPlayer($result));
        }
      } else {
        if (is_array($result)) {
          // iterate through all content items
          foreach ($result as $result_item) {
            if ($result_item instanceof Content) {
              if ($result_item->isset(CONTENT)) {
                $result_item->set(CONTENT, static::getAudioPlayer($result_item));
              }
            }
          }
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(PodcastAudioPlugin::class, "plugin", FILTER_CONTENT);
