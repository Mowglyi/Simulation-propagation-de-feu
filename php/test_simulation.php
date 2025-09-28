<?php
require_once 'Simulation.php';

use App\Simulation; 

// Configuration de la simulation
try{
    $config = [
        'foret' => [
            'hauteur' => 5,
            'largeur' => 5
        ],
        'propagation_probabilite' => 1.0, // Mettre à 1.0 pour tester la propagation maximale
        'feu_initial' => [
            ['x'=> 3, 'y' => 2]
        ]
    ];

    // Créer une instance de Simulation
    $simulation = new Simulation($config);

    $etat = 'en_cours'; //Initialisation de l'etat pour que la simulation se lance
    $etape = 0; //compteur de l'etape permettant de savoir en combien d'étapes la propagation s'est terminée.

    echo "Début de la simulation <br>";

    while($etat=='en_cours'){
        $result = $simulation->etapeSuivante();
        $etat = $result['etat'];
        $etape++;

        //Affichage des propriétés de la simulation
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