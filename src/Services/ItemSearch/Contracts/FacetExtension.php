<?php
namespace IO\Services\ItemSearch\Contracts;

use Plenty\Modules\Cloud\ElasticSearch\Lib\Search\Aggregation\AggregationInterface;

/**
 * Class FacetExtension
 * @package IO\Services\ItemSearch\Contracts
 */
interface FacetExtension
{
    /**
     * @return AggregationInterface
     */
    public function getAggregation():AggregationInterface;

    /**
     * @param array $result
     * @return array
     */
    public function mergeIntoFacetsList($result):array;

    /**
     * @param $filtersList
     * @return mixed
     */
    public function extractFilterParams($filtersList);
}