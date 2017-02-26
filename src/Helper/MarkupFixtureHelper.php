<?php

/*
 * This file is part of the MarkupFixture package.
 *
 * (c) Andrey Nilov <nilov@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\MarkupFixture\Helper;

/**
 * Class FixtureMarkupHelper
 *
 * @package Glavweb\MarkupFixture
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MarkupFixtureHelper
{
    /**
     * @var string
     */
    private $hostUrl;

    /**
     * AbstractFixtureHelper constructor.
     *
     * @param string $hostUrl
     */
    public function __construct($hostUrl)
    {
        $this->hostUrl = $hostUrl;
    }

    /**
     * @param array $fixture
     * @return array
     */
    public function prepareFixtureForMarkup(array $fixture)
    {
        $class     = $fixture['class'];
        $instances = isset($fixture['instances']) ? $fixture['instances'] : [];

        $prepared = [];
        foreach ($instances as $key => $instance) {
            foreach ($instance as $fieldName => $fieldValue) {
                $fieldDefinition = $this->getFieldDefinitionByName($class, $fieldName);

                $fieldType = $fieldDefinition['type'];
                switch ($fieldType) {
                    case 'image' :
                        $instance[$fieldName] = $this->imageMarkupData($fieldValue);
                        break;

                    case 'image_collection' :
                        $instance[$fieldName] = $this->imageCollectionMarkupData($fieldValue);
                        break;

                    case 'video' :
                        $instance[$fieldName] = $this->videoMarkupData($fieldValue);
                        break;

                    case 'video_collection' :
                        $instance[$fieldName] = $this->videoCollectionMarkupData($fieldValue);
                        break;
                }
            }
            $instance['id'] = uniqid();

            $prepared[] = $instance;
        }

        return $prepared;
    }

    /**
     * @param string $image
     * @return array
     */
    private function imageMarkupData($image)
    {
        $imagePath = $image;
        if (!$this->isExternalUri($image)) {
            $imagePath = $this->addHostUrl($image);
        }

        return [
            'id'                 => uniqid(),
            'name'               => 'Name for image',
            'description'        => 'description for image',
            'thumbnail'          => $imagePath,
            'thumbnail_path'     => $imagePath,
            'content_path'       => $image,
            'content_type'       => 'image/jpeg',
            'content_size'       => 105336,
            'width'              => null,
            'height'             => null,
            'provider_reference' => null
        ];
    }

    /**
     * @param string $video
     * @return array
     */
    private function videoMarkupData($video)
    {
        $providerReference = $this->getYouTubeProviderReferenceByUrl($video);
        $thumbnail = $this->addHostUrl('dummy/dummy_video.jpg');

        return [
            'id'                 => uniqid(),
            'name'               => 'Name for video',
            'description'        => 'description for video',
            'thumbnail'          => $thumbnail,
            'thumbnail_path'     => $thumbnail,
            'content_type'       => 'video/x-flv',
            'content_size'       => null,
            'width'              => 480,
            'height'             => 270,
            'provider_reference' => $providerReference
        ];
    }

    /**
     * @param array $imageCollection
     * @return array
     */
    private function imageCollectionMarkupData(array $imageCollection)
    {
        $prepared = [];
        foreach ($imageCollection as $image) {
            $prepared[] = $this->imageMarkupData($image);
        }

        return $prepared;
    }

    /**
     * @param array $videoCollection
     * @return array
     */
    private function videoCollectionMarkupData(array $videoCollection)
    {
        $prepared = [];
        foreach ($videoCollection as $video) {
            $prepared[] = $this->videoMarkupData($video);
        }

        return $prepared;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getYouTubeProviderReferenceByUrl($url)
    {
        if (preg_match("/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/", $url, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * @param string $fieldValue
     * @return string
     */
    private function addHostUrl($fieldValue)
    {
        return $this->hostUrl . '/' . $fieldValue;
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function isExternalUri($uri)
    {
        $components = parse_url($uri);

        return isset($components['host']) && isset($components['scheme']);
    }

    /**
     * @param array $class
     * @param $fieldName
     * @return array
     */
    private function getFieldDefinitionByName(array $class, $fieldName)
    {
        $fields = $class['fields'];

        foreach ($fields as $fieldDefinition) {
            if ($fieldDefinition['name'] == $fieldName) {
                return $fieldDefinition;
            }
        }

        throw new \RuntimeException(sprintf('The field definition "%s" is not defined.', $fieldName));
    }
}