# Simulation-propagation-de-feu

- Simulation de propagation de feu avec une API PHP et une interface HTML.
- Grâce à un fichier config.json nous configurons la taille, la probabilité ainsi que les feux initiaux permettant la propagation.
- L'API envoie l'initialisation de la forêt et permet de simuler jusqu'à ce que la propagation du feu s'arrete. 
- Le projet contient les fichiers : api.php (API), Simulation.php (Back-en du projet), index.html (Interface front-end du projet), style.css qui permet de styliser l'affichage de l'interface.
- Le projet contient aussi les tests unitaires concernant la logique du back-end du projet.
- Plusieurs autre fichiers sont présents qui comprennent les dépendances et ffichiers utiles à l'initialisation et au fonctionnement du framework PHPUnit.

-Lancement de la simulation : - ouvrir le fichier "index.html" situé dans le dossier /html.
                              - Pour lancer la simulation appuyer sur le bouton "Simuler".
                              - La simulation rend le résultat final de la simulation.
                              
- Modification de la simulation : - Pour modifier la configuration initiale il faut modifier le fichier "config.json" contenu dans le dossier config/.
                                  - Vous pouvez modifier la largeur, la hauteur, les coordonnées initiales des feux ainsi que la probabilité de propagation.
        
Théo LAFFAGE (Mowglyi)

