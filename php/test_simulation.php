<?php
require_once 'Simulation.php';

// Configuration de la simulation
try{
    $config = [
        'foret' => [
            'hauteur' => 5,
            'largeur' => 5
        ],
        'propagation_probabilite' => 0.5, // Mettez à 1.0 pour tester la propagation maximale
        'feu_initial' => [
            ['x'=> 3, 'y' => 2],
            ['x'=> 0, 'y' => 3]
        ]
    ];

    // Créer une instance de Simulation
    $simulation = new Simulation($config);


    $etat = 'en_cours';
    $etape = 0;

    echo "Début de la simulation <br>";

    while($etat=='en_cours'){
        $result = $simulation->etapeSuivante();
        $etat = $result['etat'];
        $etape++;

        echo "Etape $etape :<br>";
        echo "etat : $etat<br>";
        echo "Cases en feu : <br>";
        print_r($result['cases_en_feu']);
        echo "<br>";
        echo "Forêt : <br>";
        echo "<pre>";

        foreach($result['foret'] as $ligne){
            print_r($ligne);
        }

        echo "<pre>";
        echo "<br>";
    }

    echo "Simulation terminée après $etape étapes. <br>";
}
catch(Exception $e){
    echo "Erreur : " . $e->getMessage();
    exit;
}