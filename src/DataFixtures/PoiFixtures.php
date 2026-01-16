<?php

namespace App\DataFixtures;

use App\Entity\Poi;
use App\Entity\City;
use App\Entity\PoiCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PoiFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $citiesData = [
            'city_1' => [
                'name' => 'Paris',
                'activities' => [
                    ['name' => 'Tour Eiffel', 'address' => 'Champ de Mars, 5 Avenue Anatole France, 75007 Paris', 'description' => 'Monument emblématique de Paris.'],
                    ['name' => 'Musée du Louvre', 'address' => 'Rue de Rivoli, 75001 Paris', 'description' => 'Le plus grand musée d’art du monde.'],
                    ['name' => 'Cathédrale Notre-Dame', 'address' => '6 Parvis Notre-Dame, 75004 Paris', 'description' => 'Une cathédrale gothique célèbre.'],
                    ['name' => 'Montmartre', 'address' => 'Montmartre, 75018 Paris', 'description' => 'Quartier pittoresque avec la Basilique du Sacré-Cœur.'],
                    ['name' => 'Centre Pompidou', 'address' => 'Place Georges-Pompidou, 75004 Paris', 'description' => 'Musée d’art moderne et contemporain.']
                ],
                'accommodations' => [
                    ['name' => 'Hôtel Le Meurice', 'address' => '228 Rue de Rivoli, 75001 Paris', 'description' => 'Hôtel de luxe avec vue sur le Jardin des Tuileries.'],
                    ['name' => 'Hôtel Ritz', 'address' => '15 Place Vendôme, 75001 Paris', 'description' => 'Hôtel mythique de la place Vendôme.'],
                    ['name' => 'Hôtel Novotel Paris Centre', 'address' => '40 Rue de Londres, 75008 Paris', 'description' => 'Hôtel moderne proche de la Gare Saint-Lazare.'],
                    ['name' => 'Hôtel Lutetia', 'address' => '45 Boulevard Raspail, 75006 Paris', 'description' => 'Hôtel emblématique du quartier Saint-Germain.'],
                    ['name' => 'Hôtel Molitor', 'address' => '13 Rue Nungesser et Coli, 75016 Paris', 'description' => 'Hôtel design avec piscine célèbre.']
                ]
            ],
            'city_2' => [
                'name' => 'Lyon',
                'activities' => [
                    ['name' => 'Vieux Lyon', 'address' => '5 Rue Saint-Jean, 69005 Lyon', 'description' => 'Quartier historique de Lyon.'],
                    ['name' => 'Parc de la Tête d’Or', 'address' => '69006 Lyon', 'description' => 'Grand parc urbain avec zoo et jardins botaniques.'],
                    ['name' => 'Musée des Confluences', 'address' => '86 Quai Perrache, 69002 Lyon', 'description' => 'Musée scientifique et anthropologique.'],
                    ['name' => 'Basilique Notre-Dame de Fourvière', 'address' => '8 Place de Fourvière, 69005 Lyon', 'description' => 'Basilique emblématique sur la colline de Fourvière.'],
                    ['name' => 'Place Bellecour', 'address' => 'Place Bellecour, 69002 Lyon', 'description' => 'Grande place centrale de Lyon.']
                ],
                'accommodations' => [
                    ['name' => 'Villa Florentine', 'address' => '25 Montée Saint Barthélémy, 69005 Lyon', 'description' => 'Hôtel de luxe avec vue sur Lyon.'],
                    ['name' => 'Hôtel Carlton Lyon', 'address' => '4 Rue Jussieu, 69002 Lyon', 'description' => 'Hôtel élégant proche de la gare.'],
                    ['name' => 'Hôtel Ibis Lyon Part Dieu', 'address' => '29 Rue de la Villette, 69003 Lyon', 'description' => 'Hôtel économique et confortable.'],
                    ['name' => 'Cour des Loges', 'address' => '6 Rue du Boeuf, 69005 Lyon', 'description' => 'Hôtel charmant dans le Vieux Lyon.'],
                    ['name' => 'Hôtel de la Marne', 'address' => '22 Quai de la Marne, 69003 Lyon', 'description' => 'Hôtel simple et pratique.']
                ]
            ],
            'city_3' => [
                'name' => 'Marseille',
                'activities' => [
                    ['name' => 'Vieux-Port', 'address' => 'Vieux-Port, 13001 Marseille', 'description' => 'Port historique et animé.'],
                    ['name' => 'Basilique Notre-Dame de la Garde', 'address' => 'Rue Fort du Sanctuaire, 13281 Marseille', 'description' => 'Basilique surplombant Marseille.'],
                    ['name' => 'Calanques de Cassis', 'address' => '13260 Cassis', 'description' => 'Magnifiques calanques et sentiers de randonnée.'],
                    ['name' => 'MuCEM', 'address' => '1 Esplanade du J4, 13002 Marseille', 'description' => 'Musée des civilisations de l’Europe et de la Méditerranée.'],
                    ['name' => 'Château d’If', 'address' => 'Île d’If, 13001 Marseille', 'description' => 'Célèbre fort sur une île au large de Marseille.']
                ],
                'accommodations' => [
                    ['name' => 'InterContinental Marseille', 'address' => '17 Boulevard Charles Livon, 13007 Marseille', 'description' => 'Hôtel de luxe avec vue sur la mer.'],
                    ['name' => 'Sofitel Marseille Vieux-Port', 'address' => '36 Boulevard Charles Livon, 13007 Marseille', 'description' => 'Hôtel élégant avec vue sur le Vieux-Port.'],
                    ['name' => 'Hôtel La Résidence du Vieux-Port', 'address' => '2 Quai du Port, 13002 Marseille', 'description' => 'Hôtel charmant au centre-ville.'],
                    ['name' => 'Hôtel Carré Vieux-Port', 'address' => '15 Quai de Rive Neuve, 13007 Marseille', 'description' => 'Hôtel moderne et central.'],
                    ['name' => 'Novotel Marseille Centre Prado', 'address' => '23 Avenue Pierre Mendès, 13008 Marseille', 'description' => 'Hôtel pratique proche des plages.']
                ]
            ],
            'city_4' => [
                'name' => 'Toulouse',
                'activities' => [
                    ['name' => 'Place du Capitole', 'address' => 'Place du Capitole, 31000 Toulouse', 'description' => 'Centre historique de Toulouse.'],
                    ['name' => 'Basilique Saint-Sernin', 'address' => 'Place Saint-Sernin, 31000 Toulouse', 'description' => 'Église romane emblématique.'],
                    ['name' => 'Cité de l’Espace', 'address' => 'Avenue Jean Gonord, 31500 Toulouse', 'description' => 'Parc à thème scientifique sur l’espace.'],
                    ['name' => 'Musée des Augustins', 'address' => '21 Rue de Metz, 31000 Toulouse', 'description' => 'Musée des beaux-arts.'],
                    ['name' => 'Jardin des Plantes', 'address' => 'Allées Jules Guesde, 31000 Toulouse', 'description' => 'Parc urbain agréable pour se promener.']
                ],
                'accommodations' => [
                    ['name' => 'Hôtel Pullman Toulouse', 'address' => '4 Place Saint-Georges, 31000 Toulouse', 'description' => 'Hôtel moderne et confortable.'],
                    ['name' => 'Hôtel Crowne Plaza', 'address' => '4 Rue René Leduc, 31000 Toulouse', 'description' => 'Hôtel de luxe proche du centre-ville.'],
                    ['name' => 'Hôtel Albert 1er', 'address' => '14 Rue Gambetta, 31000 Toulouse', 'description' => 'Petit hôtel charmant au centre.'],
                    ['name' => 'Hôtel Ours Blanc', 'address' => '7 Rue Alsace Lorraine, 31000 Toulouse', 'description' => 'Hôtel économique et pratique.'],
                    ['name' => 'Hôtel des Beaux Arts', 'address' => '16 Rue des Arts, 31000 Toulouse', 'description' => 'Hôtel typique du quartier historique.']
                ]
            ],
            'city_5' => [
                'name' => 'Nice',
                'activities' => [
                    ['name' => 'Promenade des Anglais', 'address' => 'Promenade des Anglais, 06000 Nice', 'description' => 'Fameuse promenade en bord de mer.'],
                    ['name' => 'Vieux Nice', 'address' => 'Vieux Nice, 06300 Nice', 'description' => 'Quartier pittoresque et coloré.'],
                    ['name' => 'Colline du Château', 'address' => '06300 Nice', 'description' => 'Point de vue sur Nice et la baie.'],
                    ['name' => 'Musée Matisse', 'address' => '164 Avenue des Arènes de Cimiez, 06000 Nice', 'description' => 'Musée dédié à Matisse.'],
                    ['name' => 'Parc Phoenix', 'address' => '405 Promenade des Anglais, 06200 Nice', 'description' => 'Grand parc botanique avec animaux.']
                ],
                'accommodations' => [
                    ['name' => 'Hyatt Regency Nice', 'address' => '13 Promenade des Anglais, 06000 Nice', 'description' => 'Hôtel de luxe avec vue mer.'],
                    ['name' => 'Hotel Negresco', 'address' => '37 Promenade des Anglais, 06000 Nice', 'description' => 'Hôtel emblématique de la Côte d’Azur.'],
                    ['name' => 'Mercure Nice Centre', 'address' => '1 Avenue Notre Dame, 06000 Nice', 'description' => 'Hôtel pratique au centre-ville.'],
                    ['name' => 'Ibis Styles Nice Centre', 'address' => '10 Rue de la Liberté, 06000 Nice', 'description' => 'Hôtel économique et moderne.'],
                    ['name' => 'Hotel Villa Rivoli', 'address' => '15 Rue Rivoli, 06000 Nice', 'description' => 'Petit hôtel charmant.']
                ]
            ],
            // Villes 6 à 15
            'city_6' => [
                'name' => 'Bordeaux',
                'activities' => [
                    ['name' => 'Place de la Bourse', 'address' => 'Place de la Bourse, 33000 Bordeaux', 'description' => 'Place emblématique de Bordeaux.'],
                    ['name' => 'La Cité du Vin', 'address' => '134 Quai de Bacalan, 33300 Bordeaux', 'description' => 'Musée et expérience sur le vin.'],
                    ['name' => 'Pont de Pierre', 'address' => 'Pont de Pierre, 33000 Bordeaux', 'description' => 'Pont historique sur la Garonne.'],
                    ['name' => 'Jardin Public', 'address' => 'Cours de Verdun, 33000 Bordeaux', 'description' => 'Parc agréable pour se détendre.'],
                    ['name' => 'Miroir d’eau', 'address' => 'Place de la Bourse, 33000 Bordeaux', 'description' => 'Oeuvre d’eau et de reflet emblématique.']
                ],
                'accommodations' => [
                    ['name' => 'InterContinental Bordeaux', 'address' => '2-5 Place de la Comédie, 33000 Bordeaux', 'description' => 'Hôtel de luxe au centre-ville.'],
                    ['name' => 'Hotel de Sèze', 'address' => '12 Cours de l’Intendance, 33000 Bordeaux', 'description' => 'Hôtel confortable et élégant.'],
                    ['name' => 'Mercure Bordeaux Centre', 'address' => '12 Cours du Maréchal Juin, 33000 Bordeaux', 'description' => 'Hôtel pratique pour visiter Bordeaux.'],
                    ['name' => 'Hôtel Burdigala', 'address' => '12 Cours du Maréchal Juin, 33000 Bordeaux', 'description' => 'Hôtel moderne avec spa.'],
                    ['name' => 'Ibis Bordeaux Centre', 'address' => '45 Cours du 30 Juillet, 33000 Bordeaux', 'description' => 'Hôtel économique au centre-ville.']
                ]
            ],
            'city_7' => [
                'name' => 'Nantes',
                'activities' => [
                    ['name' => 'Château des Ducs de Bretagne', 'address' => '4 Place Marc Elder, 44000 Nantes', 'description' => 'Château historique au centre de Nantes.'],
                    ['name' => 'Les Machines de l’île', 'address' => 'Parc des Chantiers, 44000 Nantes', 'description' => 'Attractions mécaniques et créatives.'],
                    ['name' => 'Île de Nantes', 'address' => '44000 Nantes', 'description' => 'Quartier moderne et animé.'],
                    ['name' => 'Jardin des Plantes', 'address' => 'Boulevard Auguste Blanqui, 44000 Nantes', 'description' => 'Grand parc botanique.'],
                    ['name' => 'Passage Pommeraye', 'address' => '1 Passage Pommeraye, 44000 Nantes', 'description' => 'Galerie commerciale historique.']
                ],
                'accommodations' => [
                    ['name' => 'Radisson Blu Nantes', 'address' => '6 Quai Marcel Boissard, 44000 Nantes', 'description' => 'Hôtel moderne et confortable.'],
                    ['name' => 'Hotel Mercure Nantes Centre', 'address' => '4 Rue de Strasbourg, 44000 Nantes', 'description' => 'Hôtel pratique au centre-ville.'],
                    ['name' => 'Okko Hotels Nantes', 'address' => '5 Quai Ferdinand Favre, 44000 Nantes', 'description' => 'Hôtel design et moderne.'],
                    ['name' => 'Maison Hôtel Nantes', 'address' => '14 Rue de la Bastille, 44000 Nantes', 'description' => 'Hôtel charmant au centre.'],
                    ['name' => 'Ibis Nantes Centre', 'address' => '5 Rue du Calvaire, 44000 Nantes', 'description' => 'Hôtel économique pratique.']
                ]
            ],
            'city_8' => [
                'name' => 'Strasbourg',
                'activities' => [
                    ['name' => 'Cathédrale Notre-Dame', 'address' => 'Place de la Cathédrale, 67000 Strasbourg', 'description' => 'Cathédrale gothique célèbre.'],
                    ['name' => 'Petite France', 'address' => 'Quartier de la Petite France, 67000 Strasbourg', 'description' => 'Quartier historique pittoresque.'],
                    ['name' => 'Parlement Européen', 'address' => 'Allée du Printemps, 67000 Strasbourg', 'description' => 'Institution européenne emblématique.'],
                    ['name' => 'Musée Alsacien', 'address' => '23-25 Quai Saint-Nicolas, 67000 Strasbourg', 'description' => 'Musée sur la culture alsacienne.'],
                    ['name' => 'Jardin Botanique', 'address' => '28 Rue Goethe, 67000 Strasbourg', 'description' => 'Parc et jardin scientifique.']
                ],
                'accommodations' => [
                    ['name' => 'Hotel & Spa Le Bouclier d’Or', 'address' => '11 Rue des Couples, 67000 Strasbourg', 'description' => 'Hôtel charmant au centre.'],
                    ['name' => 'Sofitel Strasbourg Grande Ile', 'address' => '4 Place de l’Homme de Fer, 67000 Strasbourg', 'description' => 'Hôtel de luxe central.'],
                    ['name' => 'Hôtel Hannong', 'address' => '1 Place Kléber, 67000 Strasbourg', 'description' => 'Hôtel confortable proche du centre.'],
                    ['name' => 'Ibis Strasbourg Centre', 'address' => '12 Rue Kuhn, 67000 Strasbourg', 'description' => 'Hôtel économique.'],
                    ['name' => 'Maison Rouge Strasbourg', 'address' => '6 Rue de la Mésange, 67000 Strasbourg', 'description' => 'Petit hôtel charmant.']
                ]
            ],
            'city_9' => [
                'name' => 'Montpellier',
                'activities' => [
                    ['name' => 'Place de la Comédie', 'address' => 'Place de la Comédie, 34000 Montpellier', 'description' => 'Centre animé de Montpellier.'],
                    ['name' => 'Jardin des Plantes', 'address' => '2 Rue Charles de Gaulle, 34000 Montpellier', 'description' => 'Jardin botanique historique.'],
                    ['name' => 'Musée Fabre', 'address' => '39 Boulevard Bonne Nouvelle, 34000 Montpellier', 'description' => 'Musée des beaux-arts.'],
                    ['name' => 'Aqueduc Saint-Clément', 'address' => '34000 Montpellier', 'description' => 'Aqueduc historique de la ville.'],
                    ['name' => 'Cathédrale Saint-Pierre', 'address' => 'Place Peyrou, 34000 Montpellier', 'description' => 'Cathédrale gothique impressionnante.']
                ],
                'accommodations' => [
                    ['name' => 'Pullman Montpellier Centre', 'address' => '1 Place de la Comédie, 34000 Montpellier', 'description' => 'Hôtel moderne au centre-ville.'],
                    ['name' => 'Grand Hôtel du Midi', 'address' => '10 Rue du Grand Saint-Jean, 34000 Montpellier', 'description' => 'Hôtel historique confortable.'],
                    ['name' => 'Novotel Montpellier', 'address' => '5 Avenue Albert 1er, 34000 Montpellier', 'description' => 'Hôtel pratique et moderne.'],
                    ['name' => 'Hôtel Oceania Le Métropole', 'address' => '25 Place de la Comédie, 34000 Montpellier', 'description' => 'Hôtel élégant au centre.'],
                    ['name' => 'Ibis Montpellier Centre', 'address' => '14 Rue du Faubourg Figuerolles, 34000 Montpellier', 'description' => 'Hôtel économique et pratique.']
                ]
            ],
            'city_10' => [
                'name' => 'Rennes',
                'activities' => [
                    ['name' => 'Parlement de Bretagne', 'address' => 'Place du Parlement, 35000 Rennes', 'description' => 'Monument historique emblématique.'],
                    ['name' => 'Parc du Thabor', 'address' => 'Avenue du Général Patton, 35000 Rennes', 'description' => 'Grand parc urbain avec jardins et fontaines.'],
                    ['name' => 'Musée des Beaux-Arts', 'address' => '20 Quai Édouard Branly, 35000 Rennes', 'description' => 'Musée d’art et expositions temporaires.'],
                    ['name' => 'Cathédrale Saint-Pierre', 'address' => 'Place Sainte-Anne, 35000 Rennes', 'description' => 'Cathédrale emblématique de Rennes.'],
                    ['name' => 'Les Champs Libres', 'address' => '10 Cours des Alliés, 35000 Rennes', 'description' => 'Centre culturel et médiathèque.']
                ],
                'accommodations' => [
                    ['name' => 'Okko Hotels Rennes', 'address' => '8 Quai Châteaubriand, 35000 Rennes', 'description' => 'Hôtel design et moderne.'],
                    ['name' => 'Mercure Rennes Centre', 'address' => '17 Rue d’Antrain, 35000 Rennes', 'description' => 'Hôtel pratique et confortable.'],
                    ['name' => 'Hôtel Le Magic', 'address' => '12 Rue de la Monnaie, 35000 Rennes', 'description' => 'Petit hôtel charmant.'],
                    ['name' => 'Hôtel Novotel Rennes', 'address' => '15 Rue Louis Guilloux, 35000 Rennes', 'description' => 'Hôtel moderne au centre.'],
                    ['name' => 'Ibis Rennes Centre Gare', 'address' => '1 Place de la Gare, 35000 Rennes', 'description' => 'Hôtel économique proche gare.']
                ]
            ],
            'city_11' => [
                'name' => 'Dijon',
                'activities' => [
                    ['name' => 'Palais des Ducs', 'address' => 'Place de la Libération, 21000 Dijon', 'description' => 'Monument historique au centre de Dijon.'],
                    ['name' => 'Musée des Beaux-Arts', 'address' => 'Place de la Libération, 21000 Dijon', 'description' => 'Musée avec collections variées.'],
                    ['name' => 'Parc de la Colombière', 'address' => '21000 Dijon', 'description' => 'Parc historique avec allées et étangs.'],
                    ['name' => 'Église Notre-Dame', 'address' => 'Place Notre-Dame, 21000 Dijon', 'description' => 'Église gothique célèbre.'],
                    ['name' => 'Rue de la Liberté', 'address' => '21000 Dijon', 'description' => 'Rue commerçante principale de Dijon.']
                ],
                'accommodations' => [
                    ['name' => 'Grand Hôtel La Cloche', 'address' => '14 Place Darcy, 21000 Dijon', 'description' => 'Hôtel historique au centre-ville.'],
                    ['name' => 'Hôtel Oceania Dijon', 'address' => '14 Avenue Jean Jaurès, 21000 Dijon', 'description' => 'Hôtel moderne et confortable.'],
                    ['name' => 'Ibis Dijon Centre', 'address' => '5 Rue Amiral Roussin, 21000 Dijon', 'description' => 'Hôtel économique au centre.'],
                    ['name' => 'Hôtel Philippe le Bon', 'address' => '15 Rue Buffon, 21000 Dijon', 'description' => 'Hôtel charmant au centre historique.'],
                    ['name' => 'Hôtel des Ducs', 'address' => '22 Rue de la Liberté, 21000 Dijon', 'description' => 'Hôtel confortable et pratique.']
                ]
            ],
            'city_12' => [
                'name' => 'Grenoble',
                'activities' => [
                    ['name' => 'Fort de la Bastille', 'address' => 'Grenoble', 'description' => 'Fortification emblématique avec vue sur la ville.'],
                    ['name' => 'Musée de Grenoble', 'address' => '5 Place Lavalette, 38000 Grenoble', 'description' => 'Musée d’art et d’histoire.'],
                    ['name' => 'Parc Paul Mistral', 'address' => '38000 Grenoble', 'description' => 'Parc urbain avec espaces verts et statues.'],
                    ['name' => 'Téléphérique de Grenoble', 'address' => '38000 Grenoble', 'description' => 'Accès facile à la Bastille avec vue panoramique.'],
                    ['name' => 'Musée Archéologique Grenoble', 'address' => '38000 Grenoble', 'description' => 'Musée retraçant l’histoire antique de la région.']
                ],
                'accommodations' => [
                    ['name' => 'Park Hôtel Grenoble', 'address' => '6 Boulevard Maréchal Foch, 38000 Grenoble', 'description' => 'Hôtel moderne et confortable.'],
                    ['name' => 'Hôtel Mercure Grenoble', 'address' => '15 Quai Stéphane Jay, 38000 Grenoble', 'description' => 'Hôtel pratique en centre-ville.'],
                    ['name' => 'Hôtel Lesdiguières', 'address' => '7 Rue Lesdiguières, 38000 Grenoble', 'description' => 'Hôtel élégant au centre historique.'],
                    ['name' => 'Ibis Grenoble Centre', 'address' => '9 Rue Félix Poulat, 38000 Grenoble', 'description' => 'Hôtel économique.'],
                    ['name' => 'Le Grand Hôtel Grenoble', 'address' => '17 Place Victor Hugo, 38000 Grenoble', 'description' => 'Hôtel classique et confortable.']
                ]
            ],
            'city_13' => [
                'name' => 'Avignon',
                'activities' => [
                    ['name' => 'Palais des Papes', 'address' => 'Place du Palais, 84000 Avignon', 'description' => 'Monument historique emblématique.'],
                    ['name' => 'Pont d’Avignon', 'address' => '84000 Avignon', 'description' => 'Pont célèbre avec vue sur le Rhône.'],
                    ['name' => 'Place de l’Horloge', 'address' => '84000 Avignon', 'description' => 'Place centrale avec cafés et restaurants.'],
                    ['name' => 'Musée Calvet', 'address' => '65 Rue Joseph Vernet, 84000 Avignon', 'description' => 'Musée d’art et d’histoire.'],
                    ['name' => 'Rocher des Doms', 'address' => 'Place du Palais, 84000 Avignon', 'description' => 'Jardin panoramique avec vue sur la ville.']
                ],
                'accommodations' => [
                    ['name' => 'Hôtel d’Europe', 'address' => '22 Rue d’Amphoux, 84000 Avignon', 'description' => 'Hôtel de luxe historique.'],
                    ['name' => 'La Mirande', 'address' => '4 Place de l’Amirande, 84000 Avignon', 'description' => 'Hôtel élégant et charmant.'],
                    ['name' => 'Hôtel de l’Horloge', 'address' => 'Place de l’Horloge, 84000 Avignon', 'description' => 'Hôtel pratique au centre-ville.'],
                    ['name' => 'Ibis Avignon Centre', 'address' => '11 Rue Joseph Vernet, 84000 Avignon', 'description' => 'Hôtel économique central.'],
                    ['name' => 'Novotel Avignon Centre', 'address' => '2 Rue Saint Ruf, 84000 Avignon', 'description' => 'Hôtel moderne et confortable.']
                ]
            ],
            'city_14' => [
                'name' => 'Aix-en-Provence',
                'activities' => [
                    ['name' => 'Cours Mirabeau', 'address' => 'Cours Mirabeau, 13100 Aix-en-Provence', 'description' => 'Avenue emblématique bordée de cafés et platanes.'],
                    ['name' => 'Cathédrale Saint-Sauveur', 'address' => 'Place des Martyrs de la Résistance, 13100 Aix-en-Provence', 'description' => 'Cathédrale historique d’Aix.'],
                    ['name' => 'Atelier Cézanne', 'address' => '9 Avenue Paul Cézanne, 13100 Aix-en-Provence', 'description' => 'Atelier du célèbre peintre.'],
                    ['name' => 'Musée Granet', 'address' => 'Place Saint-Jean-de-Malte, 13100 Aix-en-Provence', 'description' => 'Musée d’art et archéologie.'],
                    ['name' => 'Parc de la Torse', 'address' => '13100 Aix-en-Provence', 'description' => 'Parc urbain agréable pour se promener.']
                ],
                'accommodations' => [
                    ['name' => 'Hôtel de France', 'address' => '14 Cours Mirabeau, 13100 Aix-en-Provence', 'description' => 'Hôtel central et confortable.'],
                    ['name' => 'Villa Gallici', 'address' => '18 Avenue Benjamin Franklin, 13100 Aix-en-Provence', 'description' => 'Hôtel de luxe avec jardin.'],
                    ['name' => 'Hôtel Cézanne', 'address' => '4 Boulevard Carnot, 13100 Aix-en-Provence', 'description' => 'Hôtel charmant et pratique.'],
                    ['name' => 'Ibis Aix-en-Provence', 'address' => '32 Avenue Jules Isaac, 13100 Aix-en-Provence', 'description' => 'Hôtel économique.'],
                    ['name' => 'Grand Hôtel Roi René', 'address' => '38 Cours Mirabeau, 13100 Aix-en-Provence', 'description' => 'Hôtel historique au centre-ville.']
                ]
            ],
            'city_15' => [
                'name' => 'Annecy',
                'activities' => [
                    ['name' => 'Lac d’Annecy', 'address' => '74000 Annecy', 'description' => 'Lac alpin réputé pour sa beauté et activités nautiques.'],
                    ['name' => 'Vieille Ville', 'address' => '74000 Annecy', 'description' => 'Quartier médiéval avec canaux et ruelles.'],
                    ['name' => 'Château d’Annecy', 'address' => 'Rue Royale, 74000 Annecy', 'description' => 'Château historique surplombant la ville.'],
                    ['name' => 'Pont des Amours', 'address' => '74000 Annecy', 'description' => 'Pont romantique sur le lac.'],
                    ['name' => 'Jardins de l’Europe', 'address' => '74000 Annecy', 'description' => 'Parc public avec vue sur le lac.']
                ],
                'accommodations' => [
                    ['name' => 'Hôtel Splendid', 'address' => '2 Avenue de Genève, 74000 Annecy', 'description' => 'Hôtel moderne avec vue sur le lac.'],
                    ['name' => 'Impérial Palace', 'address' => '27 Boulevard de la Corniche, 74000 Annecy', 'description' => 'Hôtel de luxe avec spa.'],
                    ['name' => 'Hôtel du Palais de l’Isle', 'address' => '3 Rue Royale, 74000 Annecy', 'description' => 'Hôtel central et charmant.'],
                    ['name' => 'Ibis Annecy Centre Vieille Ville', 'address' => '1 Rue Jean-Jacques Rousseau, 74000 Annecy', 'description' => 'Hôtel économique et pratique.'],
                    ['name' => 'Hôtel Carlton', 'address' => '6 Rue de l’Impérial, 74000 Annecy', 'description' => 'Hôtel confortable au centre.']
                ]
            ]
        ];

        foreach ($citiesData as $cityRefName => $cityData) {
            $city = $this->getReference($cityRefName, City::class);

            // Activités
            foreach ($cityData['activities'] as $activity) {
                $existingPoi = $manager->getRepository(Poi::class)->findOneBy(['name' => $activity['name']]);
                if ($existingPoi) continue;

                $poi = new Poi();
                $poi->setExternalId('act_' . $cityRefName . '_' . md5($activity['name']));
                $poi->setName($activity['name']);
                $poi->setLatitude(48.8566 + rand(-1000,1000)/10000);
                $poi->setLongitude(2.3522 + rand(-1000,1000)/10000);
                $poi->setAddress($activity['address']);
                $poi->setDescription($activity['description']);
                $poi->setContacts('contact@' . strtolower(str_replace([' ', '’'], '', $activity['name'])) . '.com');
                $poi->setClassements(rand(3,5));
                $poi->setUpdatedAt(new \DateTime());
                $poi->setCategory($this->getReference('category_5', PoiCategory::class)); // Activités
                $poi->setCity($city);
                $manager->persist($poi);
            }

            // Hébergements
            foreach ($cityData['accommodations'] as $acc) {
                $existingPoi = $manager->getRepository(Poi::class)->findOneBy(['name' => $acc['name']]);
                if ($existingPoi) continue;

                $poi = new Poi();
                $poi->setExternalId('acc_' . $cityRefName . '_' . md5($acc['name']));
                $poi->setName($acc['name']);
                $poi->setLatitude(48.8566 + rand(-1000,1000)/10000);
                $poi->setLongitude(2.3522 + rand(-1000,1000)/10000);
                $poi->setAddress($acc['address']);
                $poi->setDescription($acc['description']);
                $poi->setContacts('contact@' . strtolower(str_replace([' ', '’'], '', $acc['name'])) . '.com');
                $poi->setClassements(rand(3,5));
                $poi->setUpdatedAt(new \DateTime());
                $poi->setCategory($this->getReference('category_1', PoiCategory::class)); // Hébergements
                $poi->setCity($city);
                $manager->persist($poi);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            PoiCategoryFixtures::class,
        ];
    }
}
