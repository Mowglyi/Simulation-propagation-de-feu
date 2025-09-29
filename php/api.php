<?php
header("Contet-Type: application/json");
require_once __DIR__ . "/Simulation.php";

use App\Simulation;

//Lecture du fichier de configuration config.json
$configData = file_get_contents(__DIR__ . "/../config/config.json");

if($configData === false){
    http_response_code(500);
    echo json_encode(["error" => "Impossible de lire le fichier de configuration."]);
    exit;
}


$config = json_decode($configData, true);

if(json_last_error() !== JSON_ERROR_NONE){
    http_response_code(500);
    echo json_encode(["error" => "Erreur de décodage du fichier de configuration."]);
    exit;
}

//Récupération des données de la requête
$data = json_decode(file_get_contents("php://input"),true);

//Met à jour la configuration avec  les données de la requête 
if(isset($data["hauteur"]) && isset($data["largeur"])){
    $config["foret"]["hauteur"] = $data["hauteur"];
    $config["foret"]["largeur"] = $data["largeur"];
}

if(isset($data["feu_initial"])){
    $config["feu_initial"] = $data["feu_initial"];
}

if(isset($data["propagation_probabilite"])){
    $config["propagation_probabilite"] = $data["propagation_probabilite"];
}

try{
    //Initialisation de la simulation
    $simulation = new Simulation($config);

    if(isset($data["step"])){
        $step = $data["step"];
        if($step == -1){
            //Simulation jusqu'à la fin de la propagation
            $result = ["etat" => "en_cours", "foret" => $simulation->getGrilleForet(), "cases_en_feu" => $config["feu_initial"]];
            $currentStep = 0;
            while($result["etat"]==="en_cours"){
                $result = $simulation->etapeSuivante();
                $currentStep++;
            }
            $result["step"] = $currentStep;
        }
        else{
            for($i=0; $i < $step; $i++){
                $result = $simulation->etapeSuivante();
                if($result["etat"] === "termine"){
                    break;
                }
            }
        }
    }
    else{
        $result = ["etat" => "en_cours", "foret" => $simulation->getGrilleForet(), "cases_en_feu"=>$config["feu_initial"]];
    }

    //ajout des dimensions à la réponse de l'API 
    $result["hauteur"] = $config["foret"]["hauteur"];
    $result["largeur"] = $config["foret"]["largeur"];

    //Envoi des résultats de la simulation
    echo json_encode($result);
}
catch(Exception $e){
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}