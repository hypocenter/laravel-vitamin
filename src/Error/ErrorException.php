<?php


namespace Hypocenter\LaravelVitamin\Error;


class ErrorException extends \LogicException
{
    /**
     * @var Error
     */
    private $error;

    public function __construct(Error $error, \Throwable $previous = null)
    {
        $this->error = $error;
        parent::__construct($error->msg(), $error->value(), $previous);
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }
}