<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function setLocale($lang)
    {
        // Verifica que el idioma esté permitido (por ejemplo, 'en' y 'es')
        $availableLocales = ['en', 'es'];
        if (in_array($lang, $availableLocales)) {
            // Establece el idioma de la aplicación
            App::setLocale($lang);
            // Guarda el idioma en la sesión
            Session::put('locale', $lang);
        }

        // Redirige al usuario a la página anterior
        return redirect()->back();
    }
}