# PodcastAudioPlugin plugin
The PodcastAudioPlugin plugin is an audio player plugin for [Urlaub.be](https://github.com/urlaube/urlaube).

## Installation
Place the folder containing the plugin into your plugins directory located at `./user/plugins/`.

## Compatibility
This plugin is compatible with the configuration of the [PodcastFeedHandler handler](https://github.com/urlaube/podcastfeedhandler).

## Configuration
To configure the plugin you can change the corresponding settings in your configuration file located at `./user/config/config.php`.

### Default Background Image (Compatibility)
You can set the following value to a URL to define the default background image of the audio player:
```
Handlers::set("podcast_image", NULL);
```

## Usage
To include the audio player on a page you have to use the `[podcastaudio]` shortcode within the content. It will be replaced with the HTML sourcecode of the audio player.

To use the plugin you can add values to the header of your content files located at `/user/content/*`.

### Audio File (Compatibility)
You can set the following values to a URL and MIME-type string to define the audio file:
```
Enclosure:
EnclosureType:
```

### Background Image
You can set the following value to a URL to define the background image of the audio player:
```
AudioImage:
```

**The `AudioImage` value is used before the `Image` value and the `podcast_image` configuration value.**

### Background Image (Compatibility)
You can set the following value to a URL to define the background image of the audio player:
```
Image:
```
