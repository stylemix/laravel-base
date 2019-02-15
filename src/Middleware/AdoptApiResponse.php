<?php

namespace Stylemix\Base\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdoptApiResponse
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$response = $next($request);

		if (!$request->wantsJson()) {
			return $response;
		}

		if ($response instanceof RedirectResponse) {
			return new JsonResponse([
				'redirect' => $response->getTargetUrl()
			]);
		}

		return $response;
	}
}
