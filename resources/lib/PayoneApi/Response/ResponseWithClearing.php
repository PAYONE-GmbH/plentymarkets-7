<?php

namespace PayoneApi\Response;

class ResponseWithClearing extends GenericResponse implements ResponseContract
{
    /**
     * @var Clearing
     */
    protected $clearing;

    /** @var string|null */
    protected $requestdata = '';

    /**
     * @return string|null
     */
    public function getRequestdata(): ?string
    {
        return $this->requestdata;
    }

    /**
     * @param string|null $requestdata
     */
    public function setRequestdata(?string $requestdata): void
    {
        $this->requestdata = $requestdata;
    }

    /**
     * @param Clearing $clearing
     */
    public function setClearing($clearing)
    {
        $this->clearing = $clearing;
    }

    /**
     * Getter for Clearing
     *
     * @return Clearing
     */
    public function getClearing()
    {
        return $this->clearing;
    }
}
