<?php

namespace App\Providers;

use App\Models\Notifikasi;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer(['partials.header', 'partials.sidebar'], function ($view) {
            $me = auth()->user();

            $belumDibaca = 0;
            $komunitasSidebar = collect();

            if ($me) {
                $belumDibaca = Notifikasi::where('id_user', $me->id_user)
                    ->where('dibaca', false)
                    ->count();

                $komunitasSidebar = $me->komunitas()
                    ->orderByPivot('bergabung_pada', 'desc')
                    ->take(5)
                    ->get();
            }

            $view->with([
                'belumDibaca'      => $belumDibaca,
                'komunitasSidebar' => $komunitasSidebar,
            ]);
        });
    }
}
