<?php

namespace Scrapeio;

use DOMDocument;
use DOMXPath;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Scrapeio\Data\Collection;
use Scrapeio\Data\Node;

class Scraper
{
    protected string $query = '';
    protected string $method = 'GET';
    protected string $url;
    protected array $headers = [];
    protected ?ResponseInterface $response = null;

    public function __construct(string $method, string $url)
    {
        $this->url = $url;
        $this->method = $method;
    }

    public function scrape(bool $reFetch = false): Collection {
        if ($this->response === null || $reFetch) {
            $this->fetch();
        }

        $this->verifyHeaders();

        $text = $this->response->getBody();

        libxml_use_internal_errors(true);

        $document = new DOMDocument();
        $document->loadHTML($text);



        $xpath = new DOMXPath($document);
        $results = $xpath->evaluate($this->getQuery());

        libxml_use_internal_errors(false);

        $nodes = [];

        foreach ($results as $data) {
            $nodes[] = new Node($data);
        }

        return new Collection($nodes);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function addHeader(string $key, string $value):self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @throws GuzzleException
     */
    protected function fetch(): ResponseInterface
    {
        $client = new Client();
        $this->response =  $client
            ->request($this->getMethod(), $this->getUrl(), $this->getHeaders());

        return $this->response;
    }

    /**
     * @throws Exception
     */
    private function verifyHeaders(): void
    {
        $statusCode = $this->response->getStatusCode();

        if ($statusCode !== 200) {
            throw new Exception("Error: $statusCode. The server did not send a success response!");
        }

        $headerCollection = new Collection($this->response->getHeaders());
        $contentType = $headerCollection->filter(function($header, $key) {
            return preg_match('/Content-Type/', $key, $matches) ? $header : false;
        });

        if(!strpos(' ' .$contentType->first()[0], 'text/html')) {
            throw new Exception('Invalid response given by the server. The content type header specifies that the response is not a valid html body');
        }
    }
}