#  PodlovePlugin plugin
The PodlovePlugin plugin is an audio player plugin for [Urlaub.be](https://github.com/urlaube/urlaube) using the [Podlove Web Player](https://github.com/podlove/podlove-ui).

## Installation
Place the folder containing the plugin into your plugins directory located at `./user/plugins/`.

## Configuration
To configure the plugin you can change the corresponding settings in your configuration file located at `./user/config/config.php`.

### Subscribe Button
The subscribe button is shown as soon as a feed is set.
```php
Plugins::set("PODLOVE_FEED_URL", null);
```

A few services require additional information besides a feed:

```php
// https://podcasts.apple.com/podcast/[id]
Plugins::set("PODLOVE_APPLE_PODCAST_ID", null);

// https://www.deezer.com/en/show/[id]
Plugins::set("PODLOVE_DEEZER_ID", null);

// https://soundcloud.com/[id]
Plugins::set("PODLOVE_SOUNDCLOUD_ID", null);

// https://open.spotify.com/show/[id]
Plugins::set("PODLOVE_SPOTIFY_ID", null);

// https://www.stitcher.com/podcast/[id]
Plugins::set("PODLOVE_STITCHER_ID", null);

// https://www.youtube.com/channel/[id]
Plugins::set("PODLOVE_YOUTUBE_ID", null);

// Castbox
Plugins::set("PODLOVE_CASTBOX_ID", null);
```

### Player Settings

```php
// One of the follwing: [chapters, files, share, playlist]
Plugins::set("PODLOVE_ACTIVE_TAB", null);

// To disable embedding set it to null
Plugins::set("PODLOVE_EMBEDDING", "/share.html");

// Interactive theming engine: https://docs.podlove.org/podlove-web-player/theme.html
Plugins::set("PODLOVE_THEME_COLORS", [
        "brand" => "#166255",
        "brandDark" => "#166255",
        "brandDarkest" => "#1A3A4A",
        "brandLightest" => "#E5EAECFF",
        "shadeDark" => "#807E7C",
        "shadeBase" => "#807E7C",
        "contrast" => "#000",
        "alt" => "#fff"
      ]);

// Show share links for ["facebook","twitter","whats-app","linkedin","pinterest","xing","mail","link"]
Plugins::set("PODLOVE_SHARE_CHANNELS", ["facebook", "twitter", "mail", "link", "whats-app"]);

// Allow to add current play position to shared links 
Plugins::set("PODLOVE_SHARE_PLAYTIME", true);
```

## Usage

To include the audio player on a page you have to use the `[podloveplayer]` shortcode within the content. It will be replaced with the HTML sourcecode of the audio player.

To use the plugin you can have to values to the header of your content files located at `/user/content/*`.

### Required
You can add multiple audio files with diffrent codecs (seperated by `,`):
```
podlove_audioFiles:
podlove_audioSizes:
podlove_audioTitles:
podlove_audioMimeTypes:
podlove_duration:
```

### Optional
The following tags are optional but improve the user experience.

Additional information about the audiofile. Chapters are seperated by `,`.

```
podlove_title:
podlove_poster:
podlove_duration:

podlove_chapterTitles:
podlove_chapterStarts:
podlove_chapterLinks:
podlove_chapterImages:
```



Files to download connected to the audio file (seperated by `,`). Audio files for the player are automatically added.

```
podlove_downloadFiles:
podlove_downloadSizes:
podlove_downloadTitles:
podlove_downloadMimeTypes:
```