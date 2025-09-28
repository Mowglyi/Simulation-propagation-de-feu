public function testInitialisationForet() {
    $config = [
        'foret' => [
            'hauteur' => 5,
            'largeur' => 5
        ],
        'propagation_probabilite' => 0.5,
        'feu_initial' => []
    ];

    $simulation = new Simulation($config);

    // Vérifier que la grille a les bonnes dimensions
    $this->assertCount(5, $simulation->getGrilleForet());
    foreach ($simulation->getGrilleForet() as $ligne) {
        $this->assertCount(5, $ligne);
    }

    // Vérifier que toutes les cases sont à 0
    foreach ($simulation->getGrilleForet() as $ligne) {
        foreach ($ligne as $case) {
            $this->assertEquals(0, $case);
        }
    }
}
