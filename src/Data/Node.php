<?php

namespace Scrapeio\Data;

use DOMElement;

class Node
{
    protected DOMElement $DOMElement;
    protected string $data;

    public function __construct(DOMElement $DOMElement)
    {
        $this->DOMElement = $DOMElement;
        $this->data = $DOMElement->textContent;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function escapeHtmlAndSetData(string $data): self {
        $this->data = htmlentities(htmlspecialchars($data));
        return $this;
    }

    public function escapeHtmlAndGetData(): string
    {
        return htmlentities(htmlspecialchars($this->data));
    }

    public function getDOMElement(): DOMElement
    {
        return $this->DOMElement;
    }

    public function setDOMElement(DOMElement $DOMElement): self
    {
        $this->DOMElement = $DOMElement;
        return $this;
    }
}