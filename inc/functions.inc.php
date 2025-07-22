
<?php 

// helper function for sanitization of user inputs. It escapes special characters like <, >, &, ', and " so they can't break your HTML or inject scripts (XSS protection).

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

