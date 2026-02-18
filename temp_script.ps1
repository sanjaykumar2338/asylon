$lines = Get-Content "resources/views/admin/analytics/index.blade.php"
for ($i = 0; $i -lt $lines.Length; $i++) {
    if ($lines[$i] -like '*{{*}}*') {
        $lineNumber = $i + 1
        $lineContent = $lines[$i].Replace('`t','    ')
        Write-Output "$($lineNumber): $lineContent"
    }
}
