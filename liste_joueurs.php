<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<title>Stat joueurs</title>
<link href="https://ttepinay.fr/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" rel="stylesheet">
<style>
img { max-width: 100%; height: auto; }
</style>
</head>
<body>
<?php
$affichage_joueur = 'https://ttepinay.fr/API_FFTT.php?method=getLicenceb&club=08910652';

$jsonData = @file_get_contents($affichage_joueur);
if ($jsonData === false) {
    die('Erreur lors de la récupération des données.');
}
$data = json_decode($jsonData, true);
if (!is_array($data)) {
    die('Données JSON invalides.');
}

$filteredArray = array_values(array_filter($data, fn($item) => !empty($item['validation'])));

$keysToRemove = ['idlicence','numclub','nomclub','type','echelon','place','mutation','arb','ja','tech'];

foreach ($filteredArray as &$player) {
    foreach ($keysToRemove as $key) {
        unset($player[$key]);
    }
    $player['pointm'] = (int) $player['pointm'];
    $player['initm'] = (int) $player['initm'];
}
unset($player);

$json = json_encode(
    $filteredArray,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
);
file_put_contents('liste_joueur_complet.json', $json);
?>

<table id="ttejoueurs" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Sexe</th>
            <th>Points mensuels</th>
            <th>Points officiels</th>
            <th>Catégorie</th>
            <th>Licence</th>
            <th>Licence validée</th>
            <th>Nationalité</th>
            <th>Certificat</th>
            <th>Détail</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Sexe</th>
            <th>Points mensuels</th>
            <th>Points officiels</th>
            <th>Catégorie</th>
            <th>Licence</th>
            <th>Licence validée</th>
            <th>Nationalité</th>
            <th>Certificat</th>
            <th>Détail</th>
        </tr>
    </tfoot>
</table>
<script src="http://benalman.com/code/projects/jquery-throttle-debounce/jquery.ba-throttle-debounce.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(function() {
  fetch('https://ttepinay.fr/liste_joueur_complet.json')
    .then(r => r.json())
    .then(data => {
      const t = $('#ttejoueurs').DataTable({
        responsive: true,
        columnDefs: [{ className: 'dt-center', targets: '_all' }],
        order: [[3, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.12.1/i18n/fr-FR.json' }
      });

      const rows = data.map(({ nom, prenom, sexe, pointm, point, cat, licence, validation, natio, certif }) => {
        const details = `<a href='https://ttepinay.fr/detail_joueur/?idjoueur=${licence}'>Voir</a>`;
        const nomLien = `<a href='https://ttepinay.fr/detail_joueur/?idjoueur=${licence}'>${nom}</a>`;
        let color = 'black';
        if (sexe === 'M') color = 'royalblue';
        else if (sexe === 'F') color = 'deeppink';
        const sexeHtml = `<span style='color:${color};'>${sexe}</span>`;
        return [nomLien, prenom, sexeHtml, pointm, point, cat, licence, validation, natio, certif, details];
      });

      t.rows.add(rows).draw();
    })
    .catch(err => console.error(err));
});
</script>
</body>
</html>
