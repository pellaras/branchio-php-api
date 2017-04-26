<?php

namespace BranchIo;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class App
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class App
{
    /**
     * API Resource
     */
    const RESOURCE = '/v1/app';

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
     * Creating a New Branch App Config
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#creating-a-new-branch-app-config
     *
     * @return []
     */
    public function create(array $params = [])
    {
        $this->resolveAppParams($params, ['user_id', 'app_name', 'dev_name', 'dev_email']);

        return $this->api->request('POST', self::RESOURCE, [
            'json' => $params
        ]);
    }

    /**
     * Modifying a Branch App Config
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#updating-a-branch-app-config
     *
     * @return []
     */
    public function modify(array $params = [])
    {
        $url = sprintf("%s/%s", self::RESOURCE, $this->api->getConfig()->getBranchKey());
        $params['branch_secret'] = $this->api->getConfig()->getBranchSecret();

        $this->resolveAppParams($params, ['dev_name', 'branch_secret']);

        return $this->api->request('PUT', $url, [
            'query' => [
                'url' => $url
            ],
            'json' => $params
        ]);
    }

    /**
     * Getting Current Branch App Config
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#getting-current-branch-app-config
     *
     * @return []
     */
    public function current()
    {
        $url = sprintf("%s/%s", self::RESOURCE, $this->api->getConfig()->getBranchKey());

        return $this->api->request('GET', $url, [
            'query' => [
                'branch_secret' => $this->api->getConfig()->getBranchSecret()
            ]
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function resolveAppParams(array $params, $requiredProps = [])
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined('branch_secret')
            ->setAllowedTypes('branch_secret', 'string')
            ->setDefined('user_id')
            ->setAllowedTypes('user_id', 'int')
            ->setDefined('app_name')
            ->setAllowedTypes('app_name', 'string')
            ->setNormalizer('app_name', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('dev_name')
            ->setAllowedTypes('dev_name', 'string')
            ->setNormalizer('dev_name', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('dev_email')
            ->setAllowedTypes('dev_email', 'string')
            ->setNormalizer('dev_email', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            /**
             * Android
             */
            ->setDefined('android_app')
            ->setAllowedTypes('android_app', 'int')
            ->setAllowedValues('android_app', [0, 1, 2])
            ->setNormalizer('android_app', function (Options $options, $value) {
                if ($value > 2) {
                    return 2;
                }

                return $value;
            })
            ->setDefined('android_url')
            ->setAllowedTypes('android_url', 'string')
            ->setNormalizer('android_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('android_uri_scheme')
            ->setAllowedTypes('android_uri_scheme', 'string')
            ->setNormalizer('android_uri_scheme', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('android_package_name')
            ->setAllowedTypes('android_package_name', 'string')
            ->setNormalizer('android_package_name', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('sha256_cert_fingerprints')
            ->setDefined('android_app_links_enabled')
            ->setAllowedTypes('android_app_links_enabled', 'int')
            ->setAllowedValues('android_app_links_enabled', [0, 1])
            ->setNormalizer('android_app_links_enabled', function (Options $options, $value) {
                if ($value > 1) {
                    return 1;
                }

                return $value;
            })
            /**
             * iOS
             */
            ->setDefined('ios_app')
            ->setAllowedTypes('ios_app', 'int')
            ->setAllowedValues('ios_app', [0, 1])
            ->setNormalizer('ios_app', function (Options $options, $value) {
                if ($value > 1) {
                    return 1;
                }

                return $value;
            })
            ->setDefined('ios_url')
            ->setAllowedTypes('ios_url', 'string')
            ->setNormalizer('ios_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('ios_uri_scheme')
            ->setAllowedTypes('ios_uri_scheme', 'string')
            ->setNormalizer('ios_uri_scheme', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('ios_store_country')
            ->setAllowedTypes('ios_store_country', 'string')
            ->setNormalizer('ios_store_country', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('ios_bundle_id')
            ->setAllowedTypes('ios_bundle_id', 'int')
            ->setDefined('ios_team_id')
            ->setAllowedTypes('ios_team_id', 'int')
            ->setDefined('universal_linking_enabled')
            ->setAllowedTypes('universal_linking_enabled', 'int')
            ->setAllowedValues('universal_linking_enabled', [0, 1])
            ->setNormalizer('universal_linking_enabled', function (Options $options, $value) {
                if ($value > 1) {
                    return 1;
                }

                return $value;
            })
            /**
             * AWS Fire
             */
            ->setDefined('fire_url')
            ->setAllowedTypes('fire_url', 'string')
            ->setNormalizer('fire_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            /**
             * Windows Phone
             */
            ->setDefined('windows_phone_url')
            ->setAllowedTypes('windows_phone_url', 'string')
            ->setNormalizer('windows_phone_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            /**
             * Blackberry
             */
            ->setDefined('blackberry_url')
            ->setAllowedTypes('blackberry_url', 'string')
            ->setNormalizer('blackberry_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            /**
             * Web
             */
            ->setDefined('web_url')
            ->setAllowedTypes('web_url', 'string')
            ->setNormalizer('web_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            /**
             * Other
             */
            ->setDefined('default_desktop_url')
            ->setAllowedTypes('default_desktop_url', 'string')
            ->setNormalizer('default_desktop_url', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('text_message')
            ->setAllowedTypes('text_message', 'string')
            ->setNormalizer('text_message', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('og_app_id')
            ->setAllowedTypes('og_app_id', 'string')
            ->setNormalizer('og_app_id', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('og_title')
            ->setAllowedTypes('og_title', 'string')
            ->setNormalizer('og_title', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('og_description')
            ->setAllowedTypes('og_description', 'string')
            ->setNormalizer('og_description', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('og_image_url')
            ->setAllowedTypes('og_image_url', 'string')
            ->setNormalizer('og_image_url', function (Options $options, $value) {
                if (strlen($value) > 255) {
                    return substr($value, 0, 254);
                }

                return $value;
            })
            ->setDefined('deepview_desktop')
            ->setAllowedTypes('deepview_desktop', 'string')
            ->setNormalizer('deepview_desktop', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('deepview_ios')
            ->setAllowedTypes('deepview_ios', 'string')
            ->setNormalizer('deepview_ios', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
            })
            ->setDefined('deepview_android')
            ->setAllowedTypes('deepview_android', 'string')
            ->setNormalizer('deepview_android', function (Options $options, $value) {
                if (strlen($value) > 1024) {
                    return substr($value, 0, 1023);
                }

                return $value;
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
