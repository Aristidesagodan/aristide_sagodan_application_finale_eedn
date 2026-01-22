<?php

namespace App\Service;

use App\Entity\Poi;
use App\Entity\City;
use App\Entity\PoiCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DatatourismeImporter
{
    private EntityManagerInterface $em;
    private string $projectDir;
    private ManagerRegistry $doctrine;

    public function __construct(
        EntityManagerInterface $em, 
        string $projectDir,
        ManagerRegistry $doctrine
    ) {
        $this->em = $em;
        $this->projectDir = $projectDir;
        $this->doctrine = $doctrine;
    }

    public function import(string $pattern = 'var/data/*.csv'): array
    {
        $fullPattern = $this->projectDir . '/' . $pattern;
        $files = glob($fullPattern);

        if (empty($files)) {
            throw new \RuntimeException("Aucun fichier trouvé: $fullPattern");
        }

        $stats = ['total' => 0, 'success' => 0, 'ignored' => 0, 'errors' => 0];

        foreach ($files as $file) {
            echo "\n📄 Traitement: " . basename($file) . "\n";
            $fileStats = $this->importFile($file);
            
            $stats['total'] += $fileStats['total'];
            $stats['success'] += $fileStats['success'];
            $stats['ignored'] += $fileStats['ignored'];
            $stats['errors'] += $fileStats['errors'];
        }

        return $stats;
    }

    private function resetEntityManagerIfClosed(): bool
    {
        if (!$this->em->isOpen()) {
            try {
                $this->doctrine->resetManager();
                $this->em = $this->doctrine->getManager();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    private function importFile(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Impossible d'ouvrir: $filePath");
        }

        $header = null;
        $stats = ['total' => 0, 'success' => 0, 'ignored' => 0, 'errors' => 0];
        $batchSize = 50;
        $count = 0;
        $emClosed = false;

        $cityCache = [];
        $categoryCache = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false && !$emClosed) {
            $stats['total']++;

            if (!$header) {
                $header = $row;
                continue;
            }

            if (count($row) !== count($header)) {
                $stats['ignored']++;
                continue;
            }

            $data = array_combine($header, $row);

            // Validation
            if (empty($data['Nom_du_POI']) || 
                empty($data['Code_postal_et_commune']) ||
                empty($data['Latitude']) || 
                empty($data['Longitude'])) {
                $stats['ignored']++;
                continue;
            }

            $lat = trim($data['Latitude']);
            $lon = trim($data['Longitude']);

            if (!is_numeric($lat) || !is_numeric($lon) || ($lat == 0 && $lon == 0)) {
                $stats['ignored']++;
                continue;
            }

            try {
                // Vérifier si l'EM est ouvert
                if (!$this->em->isOpen()) {
                    if (!$this->resetEntityManagerIfClosed()) {
                        echo "\n❌ EntityManager fermé et impossible à réinitialiser. Arrêt de l'import de ce fichier.\n";
                        $emClosed = true;
                        break;
                    }
                    // Réinitialiser les caches après reset
                    $cityCache = [];
                    $categoryCache = [];
                }

                $externalId = $data['URI_ID_du_POI'] ?? uniqid('poi_', true);
                $poi = $this->em->getRepository(Poi::class)->findOneBy(['externalId' => $externalId]);

                if (!$poi) {
                    $poi = new Poi();
                    $poi->setExternalId($externalId);
                    $this->em->persist($poi);
                }

                $poi->setName(trim($data['Nom_du_POI']));
                $poi->setLatitude($lat);
                $poi->setLongitude($lon);
                $poi->setAddress($data['Adresse_postale'] ?? null);
                $poi->setDescription($data['Description'] ?? null);
                $poi->setContacts($data['Contacts_du_POI'] ?? null);
                $poi->setClassements($data['Classements_du_POI'] ?? null);

                if (!empty($data['Date_de_mise_a_jour'])) {
                    try {
                        $poi->setUpdatedAt(new \DateTime($data['Date_de_mise_a_jour']));
                    } catch (\Exception $e) {}
                }

                // Ville
                $cityInfo = explode('#', $data['Code_postal_et_commune']);
                $postalCode = $cityInfo[0] ?? null;
                $cityName = trim($cityInfo[1] ?? end($cityInfo));

                if (empty($cityName)) {
                    $stats['ignored']++;
                    continue;
                }

                $cacheKey = $postalCode . '_' . $cityName;
                if (!isset($cityCache[$cacheKey])) {
                    $city = $this->em->getRepository(City::class)->findOneBy([
                        'name' => $cityName,
                        'postalCode' => $postalCode
                    ]);

                    if (!$city) {
                        $city = new City();
                        $city->setName($cityName);
                        $city->setPostalCode($postalCode);
                        $this->em->persist($city);
                    }
                    $cityCache[$cacheKey] = $city;
                }
                $poi->setCity($cityCache[$cacheKey]);

                // Catégorie
                $categoryName = $this->extractCategory($data['Categories_de_POI'] ?? '');

                if (!isset($categoryCache[$categoryName])) {
                    $category = $this->em->getRepository(PoiCategory::class)->findOneBy(['name' => $categoryName]);

                    if (!$category) {
                        $category = new PoiCategory();
                        $category->setName($categoryName);
                        $this->em->persist($category);
                    }
                    $categoryCache[$categoryName] = $category;
                }
                $poi->setCategory($categoryCache[$categoryName]);

                $count++;
                $stats['success']++;

                // Flush par batch
                if ($count % $batchSize === 0) {
                    try {
                        $this->em->flush();
                        $this->em->clear();
                        $cityCache = [];
                        $categoryCache = [];
                    } catch (\Exception $flushException) {
                        echo "⚠️ Erreur flush au batch $count. Arrêt de l'import de ce fichier.\n";
                        echo "   Raison: " . substr($flushException->getMessage(), 0, 100) . "\n";
                        $emClosed = true;
                        break;
                    }
                }

            } catch (\Exception $e) {
                $stats['errors']++;
                
                // Si c'est une erreur qui a fermé l'EntityManager, on arrête
                if (!$this->em->isOpen()) {
                    echo "\n❌ EntityManager fermé par une erreur. Arrêt de l'import de ce fichier.\n";
                    echo "   Ligne: {$stats['total']}\n";
                    echo "   Raison: " . substr($e->getMessage(), 0, 100) . "\n";
                    $emClosed = true;
                    break;
                }
            }
        }

        fclose($handle);

        // Flush final si l'EM est toujours ouvert
        if (!$emClosed && $this->em->isOpen()) {
            try {
                $this->em->flush();
                $this->em->clear();
            } catch (\Exception $e) {
                echo "⚠️ Erreur flush final (ignorée)\n";
            }
        }

        echo "✅ Succès: {$stats['success']} | ⚠️ Ignorés: {$stats['ignored']} | ❌ Erreurs: {$stats['errors']}\n";

        return $stats;
    }

    private function extractCategory(string $categories): string
    {
        if (empty($categories)) {
            return 'Non classé';
        }

        $parts = explode('|', $categories);
        $firstCategory = trim($parts[0]);

        if (strpos($firstCategory, '#') !== false) {
            $firstCategory = trim(substr($firstCategory, strrpos($firstCategory, '#') + 1));
        }

        return !empty($firstCategory) ? $firstCategory : 'Non classé';
    }
}