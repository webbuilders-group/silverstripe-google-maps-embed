<?php
namespace WebbuildersGroup\GoogleMapsEmbed;

use Embed\Embed as CoreEmbed;
use Embed\ExtractorFactory;
use Embed\Http\Crawler;
use SilverStripe\Core\Config\Configurable;
use WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps\Extractor as GoogleMapsExtractor;

class Embed extends CoreEmbed
{
    use Configurable;

    /**
     * Google Maps API Key (supports using environment variables by referencing with `)
     * @config WebbuildersGroup\GoogleMapsEmbed\Embed.api_key
     * @var string
     */
    private static $api_key;

    /**
     * Constructor
     * @param Crawler $crawler Crawler instance
     * @param ExtractorFactory $extractorFactory Extractor Factory instance
     */
    public function __construct(?Crawler $crawler = null, ?ExtractorFactory $extractorFactory = null)
    {
        parent::__construct($crawler, $extractorFactory);

        $this->getExtractorFactory()->addAdapter('www.google.ca', GoogleMapsExtractor::class);
        $this->getExtractorFactory()->addAdapter('www.google.com', GoogleMapsExtractor::class);
    }
}
