<?php

namespace BranchIo;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Url
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class Url
{
    const RESOURCE = '/v1/url';

    /**
     * @var BranchIo
     */
    protected $api;

    /**
     * Constructor.
     *
     * @param BranchIo $api
     */
    public function __construct(BranchIo $api)
    {
        $this->api = $api;
    }

    /**
     * Creates a deep linking url
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#creating-a-deep-linking-url
     *
     * @return []
     */
    public function create(array $params = [])
    {
        $params['branch_key'] = $this->api->getConfig()->getBranchKey();

        $this->resolveUrlParams($params);

        return $this->api->request('POST', self::RESOURCE, [
            'json' => $params
        ]);
    }

    /**
     * Creates a deep linking urls
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#bulk-creating-deep-linking-urls
     *
     * @return []
     */
    public function bulkCreate(array $bulkParams = [])
    {
        $url = sprintf('%s/bulk/%s', self::RESOURCE, $this->api->getConfig()->getBranchKey());

        foreach ($bulkParams as $index => $params) {
            $bulkParams[$index]['branch_key'] = $this->api->getConfig()->getBranchKey();
            $this->resolveUrlParams($params);
        }

        return $this->api->request('POST', $url, [
            'json' => $bulkParams
        ]);
    }

    /**
     * Modifying Existing Deep Linking URLs
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#modifying-existing-deep-linking-urls
     *
     * @return []
     */
    public function modify($url, array $params = [])
    {
        $params['branch_key'] = $this->api->getConfig()->getBranchKey();
        $params['branch_secret'] = $this->api->getConfig()->getBranchSecret();
        $this->resolveUrlParams($params, ['branch_key', 'branch_secret']);

        return $this->api->request('PUT', self::RESOURCE, [
            'query' => [
                'url' => $url
            ],
            'json' => $params
        ]);
    }

    /**
     * Viewing State of Existing Deep Linking URLs
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#viewing-state-of-existing-deep-linking-urls
     *
     * @return []
     */
    public function state($url)
    {
        return $this->api->request('GET', self::RESOURCE, [
            'query' => [
                'url' => $url,
                'branch_key' => $this->api->getConfig()->getBranchKey()
            ]
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function resolveUrlParams(array $params, $requiredProps = ['branch_key'])
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined('branch_key')
            ->setAllowedTypes('branch_key', 'string')
            ->setDefined('branch_secret')
            ->setAllowedTypes('branch_secret', 'string')
            ->setDefined('data')
            ->setAllowedTypes('data', 'array')
            ->setDefined('alias')
            ->setAllowedTypes('alias', 'string')
            ->setNormalizer('alias', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('type')
            ->setAllowedTypes('type', 'int')
            ->setNormalizer('type', function (Options $options, $value) {
                if ($value > 2) {
                    return 0;
                }

                return $value;
            })
            ->setDefined('duration')
            ->setAllowedTypes('duration', 'int')
            ->setDefined('identity')
            ->setAllowedTypes('identity', 'string')
            ->setNormalizer('identity', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('channel')
            ->setAllowedTypes('channel', 'string')
            ->setNormalizer('channel', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('campaign')
            ->setAllowedTypes('campaign', 'string')
            ->setNormalizer('campaign', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('feature')
            ->setAllowedTypes('feature', 'string')
            ->setNormalizer('feature', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('stage')
            ->setAllowedTypes('stage', 'string')
            ->setNormalizer('stage', function (Options $options, $value) {
                if (strlen($value) > 128) {
                    return substr($value, 0, 127);
                }

                return $value;
            })
            ->setDefined('tags')
            ->setAllowedTypes('tags', 'array')
            ->setNormalizer('tags', function (Options $options, array $value) {
                $tags = [];

                foreach ($value as $tag) {
                    $tag = (string) $tag;
                    if (strlen($tag) > 64) {
                        return substr($tag, 0, 63);
                    }

                    $tags[] = $tag;
                }

                return $tags;
            })
        ;

        /**
         * Required properties
         */
        foreach ($requiredProps as $requiredProp) {
            $resolver->setRequired($requiredProp);
        }

        return $resolver->resolve($params);
    }
}
