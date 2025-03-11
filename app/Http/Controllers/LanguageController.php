<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function getTranslations($lang) {
        $availableLanguages = ['en', 'vi'];
    
        // Validate language input
        if (!in_array($lang, $availableLanguages)) {
            $lang = 'en'; // Default to English if the language is invalid
        }
    
        // Return the messages.json file from the selected language
        $translations = json_decode(file_get_contents(resource_path("lang/{$lang}/messages.json")), true);
    
        return response()->json($translations);
    }
    
}
