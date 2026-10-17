<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch application language between English and Myanmar.
     */
    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, ['en', 'my', 'mm'])) {
            $locale = 'en';
        }

        if ($locale === 'mm') {
            $locale = 'my';
        }

        Session::put('locale', $locale);

        return redirect()->back();
    }
}
