<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Http\Request;

class AcceptedFormat
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$formats
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, ...$formats): mixed
    {
        if (count($formats) > 0) {
            $mime_types = [];
            foreach ($formats as $format) {
                $mime_type = MimeType::fromExtension($format);
                if (!is_null($mime_type)) {
                    $mime_types[] = $mime_type;
                } else {
                    throw new Exception("$format is invalid");
                }
            }

            if ($request->accepts($mime_types)) {
                return $next($request);
            }

            abort(406, 'Must accept ' . implode(', ', $formats));
        }

        return $next($request);
    }
}