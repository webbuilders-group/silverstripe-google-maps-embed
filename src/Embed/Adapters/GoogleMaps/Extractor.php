<?php
namespace WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps;

use Embed\Extractor as Base;
use WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps\Detectors\Code;
use WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps\Detectors\Image;

class Extractor extends Base
{
    /**
     * Gets a map of custom detectors to be used for the extractor
     * @return array
     */
    public function createCustomDetectors(): array
    {
        return [
            'image' => new Image($this),
            'code' => new Code($this),
        ];
    }
}
