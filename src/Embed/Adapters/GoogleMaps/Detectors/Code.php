<?php
namespace WebbuildersGroup\GoogleMapsEmbed\Adapters\GoogleMaps\Detectors;

use BetterBrief\GoogleMapField;
use Embed\Detectors\Code as Detector;
use Embed\EmbedCode;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\HTML;
use WebbuildersGroup\GoogleMapsEmbed\Embed;

class Code extends Detector
{
    private $mode;

    /**
     * Gets the EmbedCode instance for the url
     * @return EmbedCode
     */
    public function detect(): ?EmbedCode
    {
        $key = (Embed::config()->api_key ? Injector::inst()->convertServiceProperty(Embed::config()->api_key) : GoogleMapField::config()->default_options['api_key']);
        $mode = $this->getMode();
        $uri = $this->extractor->getUri();

        // If we didn't have a maps link return a link to the asset
        if ($this->getPathSegment(0) != 'maps') {
            return new EmbedCode(
                HTML::createTag(
                    'a',
                    [
                        'href' => $uri,
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
                    $uri->__toString()
                )
            );
        }

        $settings = $this->extractor->getSettings();

        $width = (array_key_exists('min_image_width', $settings) ? $settings['min_image_width'] : 600);
        $height = (array_key_exists('min_image_height', $settings) ? $settings['min_image_height'] : 400);
        $styles = 'width:' . $width . 'px;height:' . $height . 'px;border:0;overflow:hidden;';

        switch ($mode) {
            case 'view':
                $pos = $this->getPosition();

                $html = HTML::createTag(
                    'iframe',
                    [
                        'src' => $uri
                            ->withPath('/maps/embed/v1/' . $this->mode)
                            ->withQuery(
                                http_build_query([
                                    'center' => $pos['coordinates'],
                                    'zoom' => $pos['zoom'],
                                    'key' => $key,
                                ])
                            ),
                        'frameborder' => 0,
                        'style' => $styles,
                        'allowTransparency' => 'true',
                    ]
                );

                break;
            case 'streetview':
                $pos = $this->getPosition();

                $html = HTML::createTag(
                    'iframe',
                    [
                        'src' => $uri
                            ->withPath('/maps/embed/v1/' . $this->mode)
                            ->withQuery(
                                http_build_query([
                                    'location' => $pos['coordinates'],
                                    'heading' => $pos['heading'],
                                    'pitch' => $pos['pitch'],
                                    'fov' => $pos['fov'],
                                    'key' => $key,
                                ])
                            ),
                        'frameborder' => 0,
                        'style' => $styles,
                        'allowTransparency' => 'true',
                    ]
                );

                break;
            case 'place':
            case 'search':
                $html = HTML::createTag(
                    'iframe',
                    [
                        'src' => $uri
                            ->withPath('/maps/embed/v1/' . $this->mode)
                            ->withQuery(
                                http_build_query([
                                    'q' => $this->getPathSegment(2),
                                    'key' => $key,
                                ])
                            ),
                        'frameborder' => 0,
                        'style' => $styles,
                        'allowTransparency' => 'true',
                    ]
                );

                break;
            case 'dir':
                $html = HTML::createTag(
                    'iframe',
                    [
                        'src' => $uri
                            ->withPath('/maps/embed/v1/directions')
                            ->withQuery(
                                http_build_query([
                                    'origin' => $this->getPathSegment(2),
                                    'destination' => $this->getPathSegment(3),
                                    'key' => $key,
                                ])
                            ),
                        'frameborder' => 0,
                        'style' => $styles,
                        'allowTransparency' => 'true',
                    ]
                );

                break;
            default:
                $html = HTML::createTag(
                    'a',
                    [
                        'href' => $uri,
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
                    $uri->__toString()
                );
        }

        return new EmbedCode($html, $width, $height);
    }

    /**
     * Returns parsed position data from url.
     * @param  string $mode The url mode
     * @param  Url    $url
     * @return array
     */
    protected function getPosition()
    {
        $mode = $this->getMode();

        // Set defaults
        $position = [
            'coordinates' => '',
            'zoom' => '4',
            'heading' => '0',
            'pitch' => '0',
            'fov' => '90'
        ];

        if ($mode === 'view') {
            $pos = explode(",", $this->getPathSegment(1));
            $position['coordinates'] = str_replace('@', '', $pos[0]) . ',' . $pos[1];
            $position['zoom'] = str_replace('z', "", $pos[2]);
        }

        if ($mode === 'streetview') {
            $pos = explode(",", $this->getPathSegment(1));
            $position['coordinates'] = str_replace('@', '', $pos[0]) . ',' . $pos[1];
            $position['zoom'] = str_replace('a', '', $pos[2]); // seems not used by google (emulated by other params)
            $position['heading'] = str_replace('h', '', $pos[4]);
            $position['fov'] = str_replace('y', '', $pos[3]);
            $pitch = str_replace('t', '', $pos[5]); // t is pitch but in 180% format
            if (is_numeric($pitch)) {
                $position['pitch'] = floatval($pitch) - 90;
            }
        }

        return $position;
    }

    /**
     * Detects the mode from the url for the embed
     * @return string
     */
    protected function getMode()
    {
        if (!$this->mode) {
            $mode = $this->getPathSegment(1);

            // Default is view (if mode is not mentioned in the url)
            $this->mode = 'view';

            switch ($mode) {
                case 'place':
                case 'dir':
                case 'search':
                    $this->mode = $mode;
                    break;
            }

            // check streetview mode
            // simple check,- starts with @, ends with t
            if (substr($mode, 0, 1) === '@' && substr($mode, -1) === 't') {
                $this->mode = 'streetview';
            }
        }

        return $this->mode;
    }

    /**
     * Gets the segment of the path at the given index
     * @param int $segment Index of the segment of the path starting with 0
     * @return string|bool Returns the Url's path segment or boolean false if the segment does not exist
     */
    protected function getPathSegment($segment)
    {
        $path = explode('/', ltrim($this->extractor->getUri()->getPath(), '/'));
        return (array_key_exists($segment, $path) ? $path[$segment] : false);
    }
}
