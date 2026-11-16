
document.addEventListener('DOMContentLoaded', function () {
    const languageDropdown = document.getElementById('aboutLanguage');

    const languageSections = document.querySelectorAll('[data-about-language]');

    if (!languageDropdown) return;

    function changeLanguage(language) {
        languageSections.forEach(function (section) {
            if (section.dataset.aboutLanguage === language) {
                section.hidden = false;
            } else {
                section.hidden = true;
            }
        });
    }

    // Dropdown change event
    languageDropdown.addEventListener('change', function () {
        changeLanguage(this.value);
    });

    // Default language
    changeLanguage(languageDropdown.value || 'en');
});
