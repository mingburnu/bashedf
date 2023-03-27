<?php

namespace App\Rules;

use App\Repositories\Repository;
use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class SchemaScaleMaxRule implements Rule
{
    protected Repository $repository;
    protected ?string $column;
    protected int $max;

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

        $this->max = $schema->getScale();

        if (in_array($schema->getType()->getName(), ['decimal', 'float', 'double'])) {
            if (app(NumberService::class)->isNumeric($value)) {
                return Str::length(rtrim(BigDecimal::of($value)->getFractionalPart(), '0')) <= $this->max;
            } else {
                return false;
            }
        } else {
            throw new Exception("Target is not decimal column.");
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.scale_max', ['max' => $this->max]);
    }
}
