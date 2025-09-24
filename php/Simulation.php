<?php

Class Simulation{
    private $grille_foret; //déclaration de la grille contenant la forêt
    private $casesEnFeu; //déclaration de la liste des coordonnées des cases en feu
    private $largeur; //déclaration de la largeur de la forêt
    private $hauteur; //déclaration de la hauteur de la forêt
    private $probabilite; //déclaration de la probabilité de propagation du feu

    public function __construct($config){
        //Fonction de construction prenant en argument un tableau de configuration en paramètre
        $this->hauteur = $config['foret']['hauteur']; //Initialise la var hauteur avec la hauteur du fichier config
        $this->largeur = $config['foret']['largeur']; //Initialise la var largeur avec la largeur du fichier config
        $this->probabilite = $config['propagation_probabilite']; //initialise la var probabilite avec la probabilité du fichier config
        $this->initialisationForet(); //Initialise la grille représentant la forêt
        $this->allumerFeuInitial($config['feu_initial']); //Initialise les cases initiales en feu dans la forêt
    }

    private function initialisationForet(){
        //Initialise le tableau représentant la forêt en mettant à 0 toute les cases.
        //0 ici représente les cases qui ne brulent pas
        $this->grille_foret = array_fill(0,$this->hauteur,array_fill(0,$this->largeur, 0));
    }

    private function allumerFeuInitial($feux){
        //Initilaise le/les premiers feux 
        //Ici 1 représente le feu dans la case spécifié
        foreach($feux as $feu){
            //Allume le feu à la position spécifié
            if($feu['x']>= $this->largeur || $feu['y']>= $this->hauteur || $feu['x'] < 0 || $feu['y'] < 0){
                throw new Exception("Les coordonnées initiales du feu est hors-limite de la forêt !");
            } 
            $this->grille_foret[$feu['y']][$feu['x']] = 1;
        }
        //Garde en mémoire la case en feu dans la var casesEnFeu
        $this->casesEnFeu = $feux;
    }

    public function etapeSuivante(){
        $nouveauxFeux = [];
        $nouvelleForet = $this->grille_foret;
        $casesAEteindre = [];
        $nouvellesCaseEnFeu = [];

        foreach($this->casesEnFeu as $case){
            $x = $case['x'];
            $y = $case['y'];
            $casesAEteindre[] = ['x'=>$x, 'y'=>$y];

            $voisins = $this->getVoisins($x, $y);

            foreach($voisins as $voisin){
                if($this->grille_foret[$voisin['y']][$voisin['x']] == 0 && mt_rand()/mt_getrandmax() < $this->probabilite){
                    $nouveauxFeux[] = $voisin;
                    $nouvelleForet[$voisin['y']][$voisin['x']] = 1;
                }
            }
        }

        foreach($casesAEteindre as $case){
            $nouvelleForet[$case['y']][$case['x']] = 2;
        }

        $this->grille_foret = $nouvelleForet;
        $this->casesEnFeu = $nouveauxFeux;

        return [
            'etat' => empty($this->casesEnFeu) ? 'termine' : 'en_cours',
            'foret' => $this->grille_foret,
            'cases_en_feu' => $this->casesEnFeu
        ];
    }

    private function getVoisins($x,$y){
        $voisins = [];
        if($x > 0){
            $voisins[] = ['x' => $x-1, 'y' => $y];
        }
        if($x < $this->largeur-1){
            $voisins[] = ['x' => $x+1, 'y' => $y];
        }
        if($y > 0){
            $voisins[] = ['x' => $x, 'y' => $y-1];
        }
        if($y < $this->hauteur-1){
            $voisins[] = ['x' => $x, 'y' => $y+1];
        }

        return $voisins;

    }

}