<?php
$file = 'C:\xampp\htdocs\pers_bengpus\app\Services\MarriageLetterGenerator.php';
$content = file_get_contents($file);

// Move escapeValue to the top
$escapeDef = <<<'PHP'
        $escapeValue = static function ($value) {
            if ($value === null) return '';
            $value = htmlspecialchars((string) $value, ENT_XML1, 'UTF-8');
            return str_replace(["\r\n", "\r", "\n"], '</w:t><w:br/><w:t xml:space="preserve">', $value);
        };
PHP;

$content = str_replace($escapeDef . "\n\n", "", $content); // remove from bottom

// insert at top
$insertPos = strpos($content, '$personel = $application->personel;');
$content = substr_replace($content, $escapeDef . "\n\n        ", $insertPos, 0);

// replace all setValue(x, y) with setValue(x, $escapeValue(y))
// But only those not already using $escapeValue
$content = preg_replace('/\$templateProcessor->setValue\(([^,]+),\s*(?!\\$escapeValue\()(.*?)\);/', '$templateProcessor->setValue($1, $escapeValue($2));', $content);

file_put_contents($file, $content);
echo "Rewritten!";
