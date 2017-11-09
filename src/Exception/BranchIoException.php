<?php

namespace BranchIo\Exception;

/**
 * Class BranchIoException
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class BranchIoException extends \RuntimeException
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $errors;

    /**
     * BranchIoException constructor.
     * @param string $statusCode
     * @param array $errors
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($statusCode, $errors = [], $message = '', $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->message = $message;
    }

    /**
     * Get http status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get errors list.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
