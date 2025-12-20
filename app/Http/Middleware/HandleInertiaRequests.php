<?php

namespace App\Http\Middleware;

use App\Http\Controllers\PropertyController;
use App\Models\Branch;
use App\Models\Property;
use App\Models\Category;
use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Middleware;
use Laravel\Jetstream\Http\Middleware\ShareInertiaData;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'flash' => [
                'error' => fn () => $request->session()->get('error')
            ],
            'permission' => Auth::check() ? auth()->user()->getUserPermisionByNameArray() : null,
            'logged_customer' => Auth::guard('customer')->user(), // this get logged user
            'recaptcha_site_key' => config('services.google_recaptcha.site_key'),

            /**
             * ✅ SAFE GLOBAL NAV DATA
             * These are LIGHTWEIGHT lists for the navbar only.
             * Cached to avoid repeated queries.
             */
            'navCategories' => fn () => Cache::remember('navCategories_v1', now()->addMinutes(30), function () {
                return Category::where('status', 1)
                    ->orderBy('id', 'asc')
                    ->with([
                        'subcategories' => fn ($q) => $q->where('status', 1)
                            ->orderBy('sort_order')
                            ->orderBy('title'),
                    ])
                    ->get()
                    ->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'title' => $c->title,
                            'slug' => $c->slug,
                            'subcategories' => $c->subcategories->map(fn ($s) => [
                                'id' => $s->id,
                                'title' => $s->title,
                                'slug' => $s->slug,
                                'category_id' => $s->category_id,
                            ])->values(),
                        ];
                    })
                    ->values();
            }),

            'navBrands' => fn () => Cache::remember('navBrands_v1', now()->addMinutes(60), function () {
                return Brand::where('status', 1)
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(fn ($b) => [
                        'id' => $b->id,
                        'title' => $b->title,
                        'slug' => $b->slug,
                    ])
                    ->values();
            }),
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if ($rootView = func_get_args()[2] ?? null) {
            $this->rootView = $rootView;
        }

        return parent::handle($request, $next);
    }
}
