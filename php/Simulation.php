<?php
namespace App;

use Exception;

Class Simulation{
    private $grille_foret; //déclaration de la grille contenant la forêt
    private $casesEnFeu; //déclaration de la liste des coordonnées des cases en feu
    private $largeur; //déclaration de la largeur de la forêt
    private $hauteur; //déclaration de la hauteur de la forêt
    private $probabilite; //déclaration de la probabilité de propagation du feu

    public function __construct($config){
        //Fonction de construction prenant en argument un tableau de configuration en paramètre.
        // Verification que les données principales ne sont pas manquantes.
        //Lève une exception indiquant l'erreur concernant l'appel du programme.

        if (!isset($config['feu_initial']) || empty($config['feu_initial'])) { //Verifie que le tableau de coordonnées initiales du feu n'est pas vide. 
            throw new Exception("Les coordonnées initiales du feu sont obligatoires et ne peuvent pas être vides.");
        }

        foreach ($config['feu_initial'] as $feu) { //Itération sur toute les coordonnées présentes dans le tableau de feu initial
            if (!isset($feu['x']) || !isset($feu['y'])) { //Vérifie que les coordonées sont bien rentrées.
                throw new Exception("Chaque coordonnée de feu doit contenir 'x' et 'y'.");
            }
        }

        // Vérification que foret est bien défini en largeur et en hauteur
        if (!isset($config['foret']) || !isset($config['foret']['hauteur']) || !isset($config['foret']['largeur'])) {
            throw new Exception("Les dimensions de la forêt sont obligatoires.");
        }
        
        $this->hauteur = $config['foret']['hauteur']; //Initialise la var hauteur avec la hauteur du fichier config
        $this->largeur = $config['foret']['largeur']; //Initialise la var largeur avec la largeur du fichier config
        $this->probabilite = $config['propagation_probabilite'] ?? 0.5; //initialise la var probabilite avec la probabilité du fichier config
        
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
            if(!isset($feu['x']) || !isset($feu['y'])) { //Vérifie pour chaque coordonées de feu initial que les coordonées sont présentes.
                throw new \Exception("Coordonnées de feu initial invalides: " . print_r($feu, true));
            }
            //Allume le feu à la position spécifié
            if($feu['x']>= $this->largeur || $feu['y']>= $this->hauteur || $feu['x'] < 0 || $feu['y'] < 0){ //Vérification de la validité des coordonnées pour ne pas avoir de feu hors-limite de la forêt.
                throw new Exception("Les coordonnées initiales du feu sont hors-limites de la forêt !");
            }

            $this->grille_foret[$feu['y']][$feu['x']] = 1; //Mise à jour du feu présent aux coordonnées x et y.
            
        }
        //Garde en mémoire la case en feu dans la var casesEnFeu
        $this->casesEnFeu = $feux;
    }

    public function etapeSuivante(){
        //Fonction faisant les traitements pour passer à l'étape suivante de la propagation du feu.
        $nouveauxFeux = []; //Variable contenant les coordonnées des nouveaux feux dû à la propagation
        $nouvelleForet = $this->grille_foret; //Copie de la forêt à l'étape t pour pouvoir mettre à jour les cases.
        $casesAEteindre = []; //Variable contenant les coordonnées des cases à passer en etat 'éteint'.
        $nouvellesCaseEnFeu = []; //Variable contenant les nouvelles cases à mettre en état 'feu'

        foreach($this->casesEnFeu as $case){ //Pour chaque case en feu, on récupère les coordonnées
            $x = $case['x']; //Coordonnée x
            $y = $case['y']; //Coordonnée y
            $casesAEteindre[] = ['x'=>$x, 'y'=>$y]; //ajoute au tableau les coordonnées des cases à éteindre.

            $voisins = $this->getVoisins($x, $y); //récupère les voisins de la case en feu.

            foreach($voisins as $voisin){ //Pour chaque voisin de cette case en feu 
                if($this->grille_foret[$voisin['y']][$voisin['x']] == 0 && mt_rand()/mt_getrandmax() < $this->probabilite){ //On vérifie que la case adjacente est bien dans l'état 'pas en feu' et on génère une probabilité aléatoire et 
                    //si les deux conditions sont réunis alors ob peut allumer le feu pour la case adjacente. 
                    $nouveauxFeux[] = $voisin; //On ajoute les coordonées de la case adjacente aux nouveaux feux 
                    $nouvelleForet[$voisin['y']][$voisin['x']] = 1; //Mise à jour de la forêt.
                }
            }
        }

        foreach($casesAEteindre as $case){
            $nouvelleForet[$case['y']][$case['x']] = 2; //Pour chaque case étant en feu précedemment on les éteint.
        }

        $this->grille_foret = $nouvelleForet; // Mise à jour du nouvel état de la forêt.
        $this->casesEnFeu = $nouveauxFeux; //Mise à jour du tableau contenat les cases en feux avec les nouveaux feux.

        return [
            'etat' => empty($this->casesEnFeu) ? 'termine' : 'en_cours', //On retourne termine si casesEnFeu est vide sinon la simulation est toujours en cours.
            'foret' => $this->grille_foret, //On retoutne la forêt actualisé pour cette étape.
            'cases_en_feu' => $this->casesEnFeu //On renvoie le tableau des cases en feu à cette étape.
        ];
    }

    private function getVoisins($x,$y){
        //Fonction permettant de récupérer les coordonnées des voisins de la case de coordonnées x et y.
        $voisins = [];
        //On va vérifier que les coordonnées sont valides et que l'on ne prend pas des cases hors-limites.
        if($x > 0){ //vérifie que le voisin de gauche n'est pas hors-limite
            $voisins[] = ['x' => $x-1, 'y' => $y];
        }
        if($x < $this->largeur-1){ //vérifie que le voisin de droite n'est pas hors-limite
            $voisins[] = ['x' => $x+1, 'y' => $y];
        }
        if($y > 0){ //verifie que le voisin du haut n'est pas hors-limite
            $voisins[] = ['x' => $x, 'y' => $y-1];
        }
        if($y < $this->hauteur-1){ //vérifie que le voisin du bas n'est pas hors-limite
            $voisins[] = ['x' => $x, 'y' => $y+1];
        }

        return $voisins;

    }

    public function getGrilleForet()
    {   /*//Permet de récupérer la grille simulant la forêt
        if ($this->simulation === null) { //Vérifie que la grille de la forêt existe bien 
        throw new \Exception("Aucune instance de Simulation fournie");
        }
        //Comme la grille de la forêt est un élément privée, on récupère la 
        $reflection = new ReflectionClass($this->simulation); //Utilisation de la reflexion pour accéder aux propriétés de la simulation
        $property = $reflection->getProperty('grille_foret'); //Récupère la propriété de la grille simulant la forêt
        $property->setAccessible(true); //Permet de pouvoir modifier la grille depuis l'exterieur de la classe.
        return $property->getValue($this->simulation); //Renvoie la propriété grille_foret de la simulation.
        */
        return $this->grille_foret;
    }

}