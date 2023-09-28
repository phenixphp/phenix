<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Contracts\Arrayable;
use Core\Data\Collection;
use Core\Util\URL;
use League\Uri\Components\Query;
use League\Uri\Uri;

class Paginator implements Arrayable
{
    private Query $query;
    private int $itemsEachSide = 2;
    private int $linksNumber = 5;
    private int $lastPage;

    private bool $withQueryParameters = true;

    public function __construct(
        private Uri $uri,
        private Collection $data,
        private int $total,
        private int $perPage,
        private int $currentPage
    ) {
        $this->query = Query::fromUri($this->uri);
        $this->lastPage = (int) ceil($this->total / $this->perPage);
    }

    public function withoutQueryParameters(): self
    {
        $this->withQueryParameters = false;

        return $this;
    }

    public function data(): Collection
    {
        return $this->data;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage()
    {
        return $this->currentPage < $this->lastPage;
    }

    public function from(): int
    {
        return (($this->currentPage - 1) * $this->perPage) + 1;
    }

    public function to(): int
    {
        if ($this->hasNextPage()) {
            return $this->currentPage * $this->perPage;
        }

        return $this->total;
    }
    public function links(): array
    {
        $links = [];
        $separator = ['url' => null, 'label' => '...'];

        $prepend = ($this->currentPage + 1) - $this->itemsEachSide;
        $prepend = $prepend < 0 ? 0 : $prepend;

        if ($prepend > ($this->itemsEachSide + 1)) {
            $prepend = $this->itemsEachSide;

            $links[] = $this->buildLink(1);
            $links[] = $separator;
        }

        $start = $this->currentPage - $prepend;

        for ($i=$start; $i < $this->currentPage; $i++) {
            $links[] = $this->buildLink($i);
        }

        $append = $this->linksNumber - $prepend;
        $append = ($this->currentPage + $append) > $this->lastPage
            ? ($this->lastPage - $this->currentPage) + 1
            : $append;

        $limit = $this->currentPage + $append;

        for ($i=$this->currentPage; $i < $limit; $i++) {
            $links[] = $this->buildLink($i);
        }

        if (($this->lastPage - ($this->currentPage + $append)) >= 1) {
            $links[] = $separator;
            $links[] = $this->buildLink($this->lastPage);
        }

        if (($this->lastPage - ($this->currentPage + $append)) === 0) {
            $links[] = $this->buildLink($this->lastPage);
        }

        return $links;
    }

    private function buildLink(int $page, string|int|null $label = null): array
    {
        return ['url' => $this->buildPageUrl($page), 'label' => $label ?? $page];
    }

    public function toArray(): array
    {
        return [
            'path' => URL::build($this->uri->getPath()),
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'first_page_url' => $this->getFirstPageUrl(),
            'last_page_url' => $this->getLastPageUrl(),
            'prev_page_url' => $this->getPrevPageUrl(),
            'next_page_url' => $this->getNextPageUrl(),
            'from' => $this->from(),
            'to' => $this->to(),
            'data' => $this->data->toArray(),
            'links' => $this->links(),
        ];
    }

    private function getQueryParameters(): array
    {
        return $this->withQueryParameters
            ? $this->query->parameters()
            : [];
    }

    private function buildPageUrl(int $page): string
    {
        $parameters = array_merge($this->getQueryParameters(), ['page' => $page]);

        return URL::build($this->uri->getPath(), $parameters);
    }

    private function getFirstPageUrl(): string
    {
        return $this->buildPageUrl(1);
    }

    private function getLastPageUrl(): string
    {
        return $this->buildPageUrl($this->lastPage);
    }

    private function getPrevPageUrl(): string|null
    {
        return $this->hasPreviousPage() ? $this->buildPageUrl($this->currentPage - 1) : null;
    }

    private function getNextPageUrl(): string|null
    {
        return $this->hasNextPage() ? $this->buildPageUrl($this->currentPage + 1) : null;
    }
}
