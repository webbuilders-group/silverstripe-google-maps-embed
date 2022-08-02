Google Maps Embed for Silverstripe
=================
Adds support for embedding Google Maps in Silverstripe via oEmbed

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))

## Requirements
* [Silverstripe Framework](https://github.com/silverstripe/silverstripe-framework) 4.11+
* [embed/embed](https://github.com/oscarotero/Embed) 4.0+


## Installation
```
composer require webbuilders-group/silverstripe-google-maps-embed
```


## Configuration
This module requires a Google Maps API key (you can find out how to [set that up here](https://webbuildersgroup.com/blog/how-to-create-a-google-maps-api-key/)) with at least the "[Maps Embed API](https://console.cloud.google.com/marketplace/product/google/maps-embed-backend.googleapis.com)" enabled. Then you need to adjust your yaml config to tell the module what that key is. It's highly recommended that you use an environment variable to store the API key rather than checking it into the repo.

```yaml
WebbuildersGroup\GoogleMapsEmbed\Embed:
  api_key: '`GOOGLE_MAPS_API_KEY`'

```
