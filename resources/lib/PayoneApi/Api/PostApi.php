<?php

namespace PayoneApi\Api;

use PayoneApi\Request\SerializerInterface;
use PayoneApi\Response\ClientErrorResponse;
use PayoneApi\Response\ResponseContract;
use PayoneApi\Response\ResponseFactory;

/**
 * Class PostApi
 */
class PostApi
{
    /**
     * The URL of the Payone API
     */
    const PAYONE_SERVER_API_URL = 'https://api.pay1.de/post-gateway/';

    /**
     * @var  ClientContract
     */
    protected $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $requestData = [];

    /**
     * PostApi constructor.
     *
     * @param ClientContract $client
     * @param SerializerInterface $serializer
     */
    public function __construct(ClientContract $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $client->setEndpointUrl($this->getEndPointUrl());
        $client->setMethod('POST');
        $client->addHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8');
    }

    /**
     * @return ClientContract
     */
    public function getClient(): ClientContract
    {
        return $this->client;
    }

    /**
     * @param ClientContract $client
     *
     * @return PostApi
     */
    public function setClient(ClientContract $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param object $entity
     *
     * @return ResponseContract
     */
    public function doRequest($entity)
    {
        $this->requestData = $this->serializer->serialize($entity);
        try {
            $responseBody = $this->client->doRequest($this->requestData);

            return ResponseFactory::create($responseBody);
        } catch (\Exception $e) {
        }

        return new ClientErrorResponse($e->getMessage());
    }

    /**
     * @return string
     */
    protected function getEndPointUrl()
    {
        return $this::PAYONE_SERVER_API_URL;
    }

    /**
     * @return array
     */
    public function getLastRequestData(){
        return $this->requestData;
    }
}
