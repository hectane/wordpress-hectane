<?php
// Poor man's testing. Run with:
// $ php ./test.php

function is_admin() { return false; }
function plugin_dir_path($x) { return './'; }

require_once 'hectane.php';

// Test hectane_isMessageHtml
echo (hectane_isMessageHtml("<html>hello</html>") ? "OK" : "FAILED") . "\n";
echo (!hectane_isMessageHtml("<htl>hello</html>") ? "OK" : "FAILED") . "\n";
echo (!hectane_isMessageHtml("Welcome my friend") ? "OK" : "FAILED") . "\n";
echo (!hectane_isMessageHtml("V<html>hello</html>") ? "OK" : "FAILED") . "\n";
echo (hectane_isMessageHtml("<table length=\"12\">hello</table>") ? "OK" : "FAILED") . "\n";
