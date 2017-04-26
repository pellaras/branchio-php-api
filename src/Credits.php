<?php

namespace BranchIo;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Credits
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class Credits
{
    const RESOURCE = '/v1/credits';

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
     * Getting Credit Count
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#getting-credit-count
     *
     * @return []
     */
    public function count($identity)
    {
        return $this->api->request('GET', self::RESOURCE, [
            'query' => [
                'branch_key' => $this->api->getConfig()->getBranchKey(),
                'identity' => $identity
            ]
        ]);
    }

    /**
     * Adding Credits
     *
     * See the URL https://github.com/BranchMetrics/branch-deep-linking-public-api#adding-credits
     *
     * @return []
     */
    public function add(array $params = [])
    {
        $this->resolveCreditsParams($params, ['branch_key', 'branch_secret', 'identity', 'amount']);

        return $this->api->request('POST', self::RESOURCE, [
            'json' => $params
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function resolveCreditsParams(array $params, $requiredProps = ['branch_key', 'branch_secret'])
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined('branch_key')
            ->setAllowedTypes('branch_key', 'string')
            ->setDefined('branch_secret')
            ->setAllowedTypes('branch_secret', 'string')
            ->setDefined('identity')
            ->setAllowedTypes('identity', 'string')
            ->setNormalizer('identity', function (Options $options, $value) {
                if (strlen($value) > 127) {
                    return substr($value, 0, 126);
                }

                return $value;
            })
            ->setDefined('bucket')
            ->setAllowedTypes('bucket', 'string')
            ->setNormalizer('bucket', function (Options $options, $value) {
                if (strlen($value) > 63) {
                    return substr($value, 0, 62);
                }

                return $value;
            })
            ->setDefined('amount')
            ->setAllowedTypes('amount', 'int')
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
