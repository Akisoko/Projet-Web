<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\controllers\StatistiqueController;
use App\models\StatistiqueModel;
use App\Core\View;

/**
 * Test unitaire pour le contrôleur StatistiqueController.
 * 
 * Verifie que le contrôleur récupère correctement les données du modèle
 * et les transmet à la vue.
 */
class StatistiqueControllerTest extends TestCase
{
    /**
     * Initialise l'environnement de test avant chaque méthode de test.
     */
    protected function setUp(): void
    {
        // Active le mode test du View pour capturer les données sans rendu Twig.
        View::$testMode = true;
        View::$lastRenderedData = [];
    }

    /**
     * Nettoie l'environnement après chaque test.
     */
    protected function tearDown(): void
    {
        // Réinitialise l'état global du View pour ne pas impacter les autres tests.
        View::$testMode = false;
        View::$lastRenderedData = [];
    }

    /**
     * Teste que la méthode index() agrège correctement toutes les statistiques.
     */
    public function testIndexAggregatesAllStats(): void
    {
        // 1. Préparation du mock du modèle StatistiqueModel.
        // PHPUnit 12: createMock() désactive le constructeur par défaut.
        $modelMock = $this->createMock(StatistiqueModel::class);

        // 2. Définition des comportements attendus du mock.
        $modelMock->method('countOffres')->willReturn(10);
        $modelMock->method('countEntreprises')->willReturn(5);
        $modelMock->method('countUtilisateurs')->willReturn(20);
        $modelMock->method('countCandidatures')->willReturn(30);
        $modelMock->method('moyenneCandidaturesParOffre')->willReturn(3.0);
        $modelMock->method('offresByDomaine')->willReturn([['Domaine_Offre' => 'IT', 'nombre' => 10]]);
        $modelMock->method('offresByEntreprise')->willReturn([['Nom_Entreprise' => 'Cesi', 'nombre' => 5]]);
        $modelMock->method('topWishlist')->willReturn([['Nom_Offre' => 'Stage', 'nombre' => 2]]);

        // 3. Instanciation du contrôleur avec le mock (Injection de dépendance).
        $controller = new StatistiqueController($modelMock);

        // 4. Exécution de la méthode à tester.
        $controller->index();

        // 5. Vérifications (Assertions).
        $this->assertArrayHasKey('stats', View::$lastRenderedData, 'Les données transmises à la vue doivent contenir une clé "stats".');
        
        $stats = View::$lastRenderedData['stats'];
        
        $this->assertEquals(10, $stats['total_offres']);
        $this->assertEquals(5, $stats['total_entreprises']);
        $this->assertEquals(20, $stats['total_utilisateurs']);
        $this->assertEquals(30, $stats['total_candidatures']);
        $this->assertEquals(3.0, (float)$stats['moyenne_candidatures']);
        $this->assertCount(1, $stats['offres_par_domaine']);
        $this->assertCount(1, $stats['offres_par_entreprise']);
        $this->assertCount(1, $stats['top_wishlist']);
    }
}
