<?php
function csv_celula($valor): string
{
    $texto = (string) $valor;
    // evita que o Excel execute fórmulas escritas em campos de texto
    return ($texto !== '' && strpos('=+-@', $texto[0]) !== false) ? "'" . $texto : $texto;
}

function csv_enviar(string $ficheiro, array $cabecalho, array $linhas): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $ficheiro . '"');
    $saida = fopen('php://output', 'w');
    fwrite($saida, "\xEF\xBB\xBF");
    fputcsv($saida, $cabecalho, ';', '"', '');
    foreach ($linhas as $linha) {
        fputcsv($saida, array_map('csv_celula', array_values($linha)), ';', '"', '');
    }
    fclose($saida);
    exit;
}
