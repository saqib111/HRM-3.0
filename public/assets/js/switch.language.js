// <!-- LANGUAGE SCRIPT STARTS -->
$(document).ready(function() {
    let translations = {};
    const storedLang = localStorage.getItem('language');

    const initialLang = storedLang || 'en';
    // const initialLang = storedLang || '{{ app()->getLocale() }}';  // Default to Laravel locale
    const initialLangText = initialLang === 'en' ? 'English' : 'Tiếng Việt';

    // $('#language-toggle img').attr('src', initialLang === 'en' ? $('#language-selector .dropdown-item[data-lang="en"]').data('flag') : $('#language-selector .dropdown-item[data-lang="vi"]').data('flag'));
    $('#language-toggle img').attr('src', initialLang === 'en' ? $('#language-selector .dropdown-item[data-lang="en"]').data('flag') : $('#language-selector .dropdown-item[data-lang="vi"]').data('flag'));
    $('#selected-language').text(initialLangText); // Set the language name in the dropdown

    loadTranslations(initialLang);

    // Language change handler for the dropdown
    $('#language-selector .dropdown-item').click(function() {
        const selectedLang = $(this).data('lang');
        const selectedLangText = selectedLang === 'en' ? 'English' : 'Tiếng Việt';

        // Update flag and language name in the toggle
        $('#language-toggle img').attr('src', $(this).data('flag'));
        $('#selected-language').text(selectedLangText);

        // Store selected language in localStorage
        localStorage.setItem('language', selectedLang);
        loadTranslations(selectedLang);
    });

    // Load translations from server or cache
    function loadTranslations(lang) {
        if (translations[lang]) {
            updateLanguageContent(translations[lang]);
        } else {
            $.ajax({
                url: '/get-translations/' + lang,
                method: 'GET',
                success: function(response) {
                    translations[lang] = response;
                    updateLanguageContent(response);
                },
                error: function() {
                    alert('Error loading translations!');
                }
            });
        }
    }

    function updateLanguageContent(translations) {
        $('[data-translate]').each(function() {
            var key = $(this).data('translate');
            if (translations[key]) {
                $(this).text(translations[key]);
            }
        });
    }
});
// <!-- LANGUAGE SCRIPT ENDS -->