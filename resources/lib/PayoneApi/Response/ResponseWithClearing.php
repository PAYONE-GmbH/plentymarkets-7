<?php

namespace PayoneApi\Response;

class ResponseWithClearing extends GenericResponse implements ResponseContract
{
    /**
     * @var Clearing
     */
    protected $clearing;

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
