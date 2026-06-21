<?php

/**
 * Laravel IDE stubs — parsed by Intelephense for type resolution.
 * This file is never executed; it exists only to provide class/function
 * declarations so the IDE can resolve types without a fully-indexed vendor.
 */

namespace Illuminate\Database\Eloquent {

    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Query\Builder as QueryBuilder;

    abstract class Model
    {
        public static function create(array $attributes = []): static {}
        public static function find(mixed $id, array $columns = ['*']): static|null {}
        public static function findOrFail(mixed $id, array $columns = ['*']): static {}
        public static function all(array $columns = ['*']): Collection {}
        public static function count(): int {}
        public static function sum(string $column): float|int {}
        public static function where(string|\Closure $column, mixed $operator = null, mixed $value = null): Builder {}
        public static function whereIn(string $column, array $values): Builder {}
        public static function whereNotNull(string $column): Builder {}
        public static function whereNull(string $column): Builder {}
        public static function whereYear(string $column, int $year): Builder {}
        public static function whereMonth(string $column, int $month): Builder {}
        public static function with(array|string $relations): Builder {}
        public static function select(mixed ...$columns): Builder {}
        public static function latest(string $column = 'created_at'): Builder {}
        public static function orderBy(string $column, string $direction = 'asc'): Builder {}
        public static function orderByDesc(string $column): Builder {}
        public static function whereHas(string $relation, \Closure $callback = null): Builder {}
        public static function withCount(array|string $relations): Builder {}
        public static function query(): Builder {}
        public static function newModelQuery(): Builder {}
        public static function newQuery(): Builder {}
        public static function first(array $columns = ['*']): static|null {}
        public static function firstOrCreate(array $attributes = [], array $values = []): static {}

        public function update(array $attributes = [], array $options = []): bool {}
        public function delete(): bool|null {}
        public function save(array $options = []): bool {}
        public function load(array|string $relations): static {}
        public function fresh(array|string $relations = []): static|null {}
        public function refresh(): static {}

        /** @return BelongsTo<static, static> */
        protected function belongsTo(string $related, string $foreignKey = null, string $ownerKey = null, string $relation = null): BelongsTo {}
        /** @return HasMany<static> */
        protected function hasMany(string $related, string $foreignKey = null, string $localKey = null): HasMany {}
        /** @return HasOne<static> */
        protected function hasOne(string $related, string $foreignKey = null, string $localKey = null): HasOne {}
        /** @return BelongsToMany<static> */
        protected function belongsToMany(string $related, string $table = null, string $foreignPivotKey = null, string $relatedPivotKey = null): BelongsToMany {}

        /** @return mixed */
        public function __get(string $key): mixed {}
        public function __set(string $key, mixed $value): void {}
        public function __isset(string $key): bool {}
    }

    class Builder
    {
        public function where(string|\Closure $column, mixed $operator = null, mixed $value = null): static {}
        public function whereIn(string $column, array $values): static {}
        public function whereNotNull(string $column): static {}
        public function whereNull(string $column): static {}
        public function whereYear(string $column, int $year): static {}
        public function whereMonth(string $column, int $month): static {}
        public function whereHas(string $relation, \Closure $callback = null): static {}
        public function with(array|string $relations): static {}
        public function withCount(array|string $relations): static {}
        public function select(mixed ...$columns): static {}
        public function latest(string $column = 'created_at'): static {}
        public function orderBy(string $column, string $direction = 'asc'): static {}
        public function orderByDesc(string $column): static {}
        public function get(array $columns = ['*']): Collection {}
        public function first(array $columns = ['*']): Model|null {}
        public function firstOrFail(array $columns = ['*']): Model {}
        public function count(): int {}
        public function sum(string $column): float|int {}
        public function pluck(string $column, string $key = null): Collection {}
        public function paginate(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator {}
        public function exists(): bool {}
        public function update(array $values): int {}
        public function delete(): int {}
        public function groupBy(string ...$groups): static {}
        public function take(int $value): static {}
        public function limit(int $value): static {}
        public function withQueryString(): static {}
    }

    class Collection {}

}

namespace Illuminate\Database\Eloquent\Factories {
    trait HasFactory
    {
        public static function factory(int $count = null, array $state = []): object {}
    }
}

namespace Illuminate\Database\Eloquent\Relations {
    class BelongsTo
    {
        public function first(): mixed {}
        public function get(): \Illuminate\Database\Eloquent\Collection {}
    }
    class HasMany
    {
        public function where(string $column, mixed $operator = null, mixed $value = null): \Illuminate\Database\Eloquent\Builder {}
        public function latest(string $column = 'created_at'): \Illuminate\Database\Eloquent\Builder {}
        public function first(): mixed {}
        public function get(): \Illuminate\Database\Eloquent\Collection {}
        public function create(array $attributes = []): mixed {}
        public function update(array $values): int {}
        public function delete(): int {}
        public function exists(): bool {}
        public function count(): int {}
    }
    class HasOne
    {
        public function first(): mixed {}
        public function get(): \Illuminate\Database\Eloquent\Collection {}
    }
    class BelongsToMany
    {
        public function where(string $column, mixed $operator = null, mixed $value = null): \Illuminate\Database\Eloquent\Builder {}
        public function get(): \Illuminate\Database\Eloquent\Collection {}
    }
}

namespace Illuminate\Foundation\Auth {
    abstract class User extends \Illuminate\Database\Eloquent\Model
    {
        protected $hidden = [];
        protected $fillable = [];
    }
}

namespace Illuminate\Support {

    class Carbon extends \DateTime
    {
        public static function now(string|\DateTimeZone $tz = null): static {}
        public static function parse(mixed $time = null, mixed $tz = null): static {}
        public function format(string $format): string {}
        public function addMinutes(int $value): static {}
        public function addDays(int $value): static {}
        public function subDays(int $value): static {}
        public function isPast(): bool {}
        public function isFuture(): bool {}
        public function toDateTimeString(): string {}
        public function diffForHumans(): string {}
    }

    class Collection implements \Countable, \JsonSerializable
    {
        public function map(\Closure $callback): static {}
        public function filter(\Closure $callback = null): static {}
        public function each(\Closure $callback): static {}
        public function first(\Closure $callback = null, mixed $default = null): mixed {}
        public function last(\Closure $callback = null, mixed $default = null): mixed {}
        public function count(): int {}
        public function isEmpty(): bool {}
        public function isNotEmpty(): bool {}
        public function pluck(string|array $value, string $key = null): static {}
        public function where(string $key, mixed $operator = null, mixed $value = null): static {}
        public function groupBy(callable|string $groupBy): static {}
        public function values(): static {}
        public function toArray(): array {}
        public function jsonSerialize(): mixed {}
        public function hasPages(): bool {}
        public function sum(callable|string $callback = null): mixed {}
    }

    class Str
    {
        public static function uuid(): object {}
        public static function random(int $length = 16): string {}
        public static function slug(string $title, string $separator = '-'): string {}
        public static function padLeft(string $string, int $length, string $pad = ' '): string {}
        public static function upper(string $value): string {}
        public static function lower(string $value): string {}
    }
}

namespace Illuminate\Http {
    class Request
    {
        public function input(string $key = null, mixed $default = null): mixed {}
        public function all(array $keys = null): array {}
        public function get(string $key, mixed $default = null): mixed {}
        public function filled(string|array $key): bool {}
        public function has(string|array $key): bool {}
        public function hasFile(string $key): bool {}
        public function file(string $key = null): mixed {}
        public function validate(array $rules, array $messages = [], array $customAttributes = []): array {}
        public function merge(array $input): static {}
        public function boolean(string $key = null, bool $default = false): bool {}
        public function integer(string $key, int $default = 0): int {}
        public function getMethod(): string {}
        public function isMethod(string $method): bool {}
        public function user(): mixed {}
        public function route(string $param = null): mixed {}
        public function ip(): string|null {}
        public function url(): string {}
        public function fullUrl(): string {}
        public function path(): string {}
        public function is(string ...$patterns): bool {}
        public function ajax(): bool {}
        public function wantsJson(): bool {}
        public function __get(string $key): mixed {}
    }

    class RedirectResponse
    {
        public function with(string|array $key, mixed $value = null): static {}
        public function withErrors(mixed $provider, string $key = 'default'): static {}
        public function withInput(array $input = null): static {}
    }
}

namespace Illuminate\Pagination {
    class LengthAwarePaginator extends \Illuminate\Support\Collection
    {
        public function hasPages(): bool {}
        public function hasMorePages(): bool {}
        public function onFirstPage(): bool {}
        public function currentPage(): int {}
        public function lastPage(): int {}
        public function previousPageUrl(): string|null {}
        public function nextPageUrl(): string|null {}
        public function withQueryString(): static {}
    }
}

namespace Illuminate\Container {
    class Container
    {
        public static function getInstance(): static {}
        public function make(string $abstract, array $parameters = []): mixed {}
        public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void {}
    }
}

namespace {
    function abort(int $code, string $message = '', array $headers = []): never {}
    function abort_if(bool $boolean, int $code, string $message = '', array $headers = []): void {}
    function abort_unless(bool $boolean, int $code, string $message = '', array $headers = []): void {}
    function app(string $abstract = null, array $parameters = []): mixed {}
    function auth(string $guard = null): \Illuminate\Contracts\Auth\Guard {}
    function back(int $status = 302, array $headers = []): \Illuminate\Http\RedirectResponse {}
    function config(array|string $key = null, mixed $default = null): mixed {}
    function now(\DateTimeZone|string $tz = null): \Illuminate\Support\Carbon {}
    function redirect(string $to = null, int $status = 302, array $headers = [], bool $secure = null): \Illuminate\Http\RedirectResponse {}
    function request(string $key = null, mixed $default = null): mixed {}
    function route(string $name, mixed $parameters = [], bool $absolute = true): string {}
    function session(array|string $key = null, mixed $default = null): mixed {}
    function view(string $view = null, array $data = [], array $mergeData = []): mixed {}
    function asset(string $path, bool $secure = null): string {}
    function storage_path(string $path = ''): string {}
    function public_path(string $path = ''): string {}
    function base_path(string $path = ''): string {}
}
