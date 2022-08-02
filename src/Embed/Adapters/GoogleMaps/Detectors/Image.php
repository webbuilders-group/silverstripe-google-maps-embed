<?php
namespace WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps\Detectors;

use Embed\Detectors\Image as Detector;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use SilverStripe\Control\Director;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

class Image extends Detector
{
    /**
     * Gets the uri to the placeholder image
     * @return UriInterface
     */
    public function detect(): ?UriInterface
    {
        return new Uri(Director::absoluteURL(ModuleResourceLoader::resourceURL('webbuilders-group/silverstripe-google-maps-embed: images/google-maps-logo.svg')));
    }
}
