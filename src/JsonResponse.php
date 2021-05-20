<?php

declare(strict_types=1);

namespace StructuredHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse as JsonResponseDiactoros;
use Lukasoppermann\Httpstatus\Httpstatus;

/**
 * Class ResponseJson
 * @package App
 */
class JsonResponse extends JsonResponseDiactoros
{
    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $metaMessage;

    /**
     * @var bool
     */
    private $isError;

    /**
     * @var string
     */
    private $httpReasonPhrase;

    /**
     * @var array
     */
    private $pagination;

    /**
     * Response constructor.
     * @param array $data
     * @param string $metaMessage
     * @param int $statusCode
     * @param bool $isError
     * @param array $headers
     */
    public function __construct(
        array $data = [],
        string $metaMessage = '',
        int $statusCode = StatusCodeInterface::STATUS_OK,
        bool $isError = false,
        array $headers = []
    )
    {
        $this->data = $data;
        $this->metaMessage = $metaMessage;
        $this->httpStatusCode = $statusCode;
        $this->isError = $isError;
        $this->httpReasonPhrase = (new Httpstatus())->getReasonPhrase($statusCode);
        $this->headers = $headers;
        $this->pagination = [];
        $this->buildResponse();
        parent::__construct($this->data, $this->httpStatusCode, $this->headers);
    }

    /**
     * Build payload
     */
    private function buildResponse(): void
    {
        $this->data = [
            'metadata' => [
                'is_error' => $this->isError,
                'http_status' => $this->httpStatusCode,
                'http_status_phrase' => $this->httpReasonPhrase,
                'time' => time(),
                'message' => $this->metaMessage
            ],
            'data' => $this->data,
            'pagination' => []
        ];
    }

    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $totalItems
     * @param int $totalPages
     * @return JsonResponseDiactoros
     */
    public function buildWithPagination(
        int $currentPage = 1,
        int $itemsPerPage = 1,
        int $totalItems = 1,
        int $totalPages = 1
    ): JsonResponseDiactoros
    {
        $this->pagination = [
            'current_page' => $currentPage,
            'items_per_page' => $itemsPerPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
        ];
        $this->data['pagination'] = $this->pagination;
        return $this->withPayload($this->data);
    }
}