<?php

namespace App\Support;

use Illuminate\Http\Request as BaseRequest;

/**
 * @method bool filled(string $key)
 * @method bool hasFile(string $key)
 * @method mixed input(string $key, mixed $default = null)
 * @method mixed file(string $key = null)
 * @method void validate(array $rules)
 * @method void merge(array $input)
 * @method array all()
 */
class LaravelRequest extends BaseRequest
{
}
