<?php

namespace App\Rules;

use App\Repositories\Repository;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class SchemaStringMaxRule implements Rule
{
    protected Repository $repository;
    protected ?string $column;
    protected ?int $max;

    /**
     * Create a new rule instance.
     *
     * @param Repository $repository
     * @param string|null $column
     */
    public function __construct(Repository $repository, string $column = null)
    {
        $this->repository = $repository;
        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool
    {
        if (is_null($this->column)) {
            $schema = $this->repository->getFieldSchema(Str::of($attribute)->split('[\.]')->last());
        } else {
            $schema = $this->repository->getFieldSchema($this->column);
        }

        if (in_array($schema->getType()->getName(), ['string','text'])) {
            $this->max = $schema->getLength();
        } else {
            throw new Exception("Target is not text column.");
        }

        return Str::length($value) <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.max.string', ['max' => $this->max ?? 0]);
    }
}
