<?php

declare(strict_types=1);

namespace DTO;

/**
 * The class transforms the data into the desired format
 */
class BookDTO
{
    public string $title;
    public ?string $isbn;
    public int $page_count;
    public ?string $published_date;
    public ?string $thumbnail_url;
    public ?string $short_description;
    public ?string $long_description;
    public ?string $status_id;

    /**
     * Transforms the data to desired format
     *
     * @param  array  $data
     * @return BookDTO
     */
    public static function transform(array $data): self
    {
        $dto = new self();

        $dto->title = $data['title'];
        $dto->isbn = $data['isbn'] ?? null;
        $dto->page_count = $data['pageCount'] ?? 0;
        $dto->published_date = $dto->transformPublishedDate($data);
        $dto->thumbnail_url = $data['thumbnailUrl'] ?? null;
        $dto->short_description = $data['shortDescription'] ?? null;
        $dto->long_description = $data['longDescription'] ?? null;
        $dto->status_id = $data['status'] ?? null;

        return $dto;
    }

    /**
     * Returns the data as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(get_class_vars(get_class($this)), get_object_vars($this));
    }

    /**
     * Transforming the "publishedDate" field in the request data into a different date format
     *
     * @param  array  $data
     * @return string|null
     */
    private function transformPublishedDate(array $data): ?string
    {
        if (isset($data['publishedDate']) && !empty($data['publishedDate'])) {
            return date_create(array_values($data['publishedDate'])[0])->format('Y-m-d H:i:s');
        }

        return null;
    }
}