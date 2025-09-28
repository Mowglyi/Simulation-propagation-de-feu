<?php
use PHPUnit\Framework\TestCase;
use App\Simulation;

class SimulationTest extends TestCase
{
    private $simulation;

    protected function setUp(): void
    {
        // Configuration valide avec feu initial
        $config = [
            'foret' => [
                'hauteur' => 5,
                'largeur' => 5
            ],
            'propagation_probabilite' => 0.5,
            'feu_initial' => [
                ['x' => 1, 'y' => 1]  // Coordonnées valides
            ]
        ];
        $this->simulation = new Simulation($config);
    }

    private function getGrilleForet($simulation = null)
    {   //Pareil que dans Simulation.php permet de récupérer la grille simulant la forêt
        $simulation = $simulation ?? $this->simulation; 
        $reflection = new ReflectionClass($simulation);
        $property = $reflection->getProperty('grille_foret');
        $property->setAccessible(true);
        return $property->getValue($simulation);
    }

    public function testConstructeurAvecFeuInitialVide()
    {   //Fonction permettant de tester si les coordonées initiales de feux sont légitimes et que le code lève bien une exception pour signaler l'erreur
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Les coordonnées initiales du feu sont obligatoires et ne peuvent pas être vides.");

        $config = [
            'foret' => [
                'hauteur' => 5,
                'largeur' => 5
            ],
            'propagation_probabilite' => 0.5
            // feu_initial est intentionnellement omis
        ];

        new Simulation($config);
    }

    public function testConstructeurAvecFeuInitialTableauVide()
    {   //Pareil que la fonction d'avant seulement le feu_initizl existe et vide
        //On vérifie que le code lève bien l'exception requise.
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Les coordonnées initiales du feu sont obligatoires et ne peuvent pas être vides.");

        $config = [
            'foret' => [
                'hauteur' => 5,
                'largeur' => 5
            ],
            'propagation_probabilite' => 0.5,
            'feu_initial' => [] // Tableau vide
        ];

        new Simulation($config);
    }

    public function testInitialisationForet()
    {   
        //On vérifie que l'initialistion de la grille de la forêt est conforme à ce qui est attendu
        $grille = $this->getGrilleForet();
        $this->assertCount(5, $grille);
        foreach ($grille as $ligne) {
            $this->assertCount(5, $ligne);
        }
    }

    public function testAllumerFeuInitial()
    {   
        //On vérifie que les feux allumés à l'état initial sont bien pris en compte.
        $config = [
            'foret' => [
                'hauteur' => 5,
                'largeur' => 5
            ],
            'propagation_probabilite' => 0.5,
            'feu_initial' => [
                ['x' => 1, 'y' => 1],
                ['x' => 2, 'y' => 2]
            ]
        ];

        $simulation = new Simulation($config);
        $this->assertCount(2, $config['feu_initial']);
        $grille = $this->getGrilleForet($simulation);
        $this->assertEquals(1, $grille[1][1], "La case (1,1) devrait être en feu");
        $this->assertEquals(1, $grille[2][2], "La case (2,2) devrait être en feu");
    }

    public function testCoordonneesHorsLimites()
    {   
        //On vérifie ici que les coordonnées de feeux initiaux sont bien dans les limites de la forêt et sinon que
        //l'exception levée est celle attendue.
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Les coordonnées initiales du feu sont hors-limites de la forêt !");

        $config = [
            'foret' => [
                'hauteur' => 5,
                'largeur' => 5
            ],
            'propagation_probabilite' => 0.5,
            'feu_initial' => [
                ['x' => 5, 'y' => 5]
            ]
        ];

        new Simulation($config);
    }

    public function testGetVoisins()
    {   
        //Fonction permettant de tester si l'on récupère bien les bonnes coordonnées de voisins d'une case en feu
        $reflection = new ReflectionClass($this->simulation);
        $method = $reflection->getMethod('getVoisins');
        $method->setAccessible(true);

        $voisins = $method->invoke($this->simulation, 2, 2);
        $this->assertCount(4, $voisins);

        $voisins = $method->invoke($this->simulation, 0, 0);
        $this->assertCount(2, $voisins);
    }

    /**
     * @dataProvider providerTestPropagation
     */
    public function testPropagationFeu($config, $expected)
    {   
        //Fonction permettant de tester si le résultat est bien celui attendu c'est à dire que pour une probabilité de 1 
        //l'etat est 'en_cours' pour l'etape suivante et pour la probabilité 0 que c'est 'terminé' car tout les feux ont été éteints à l'étape suivante.
        $simulation = new Simulation($config);
        $result = $simulation->etapeSuivante();
        $this->assertEquals($expected, $result['etat']);
    }

    public function providerTestPropagation()
    {
        return [
            'Feu se propage' => [
                [
                    'foret' => ['hauteur' => 5, 'largeur' => 5],
                    'propagation_probabilite' => 1.0,
                    'feu_initial' => [['x' => 2, 'y' => 2]]
                ],
                'en_cours'
            ],
            'Feu ne se propage pas' => [
                [
                    'foret' => ['hauteur' => 5, 'largeur' => 5],
                    'propagation_probabilite' => 0.0,
                    'feu_initial' => [['x' => 2, 'y' => 2]]
                ],
                'termine'
            ]
        ];
    }
}
