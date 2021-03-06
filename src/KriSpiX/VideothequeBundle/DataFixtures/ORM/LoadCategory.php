<?php

namespace KriSpiX\VideothequeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KriSpiX\VideothequeBundle\Entity\Movie;
use KriSpiX\VideothequeBundle\Entity\Genre;
use KriSpiX\VideothequeBundle\Entity\Format;
use KriSpiX\VideothequeBundle\Entity\Keyword;

class LoadCategory implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            "Action",
            "Espionnage",
            "Comédie",
            "Aventure",
            "Pour enfants",
            "Animation",
            "Thriller",
            "Policier",
            "Catastrophe",
            "Science Fiction",
            "Western",
            "Comédie dramatique",
            "Biopic / Biographie",
            "Drame",
            "Animaux",
            "Série TV",
            "Historique",
            "Horreur",
            "Fantastique",
            "Spectacle",
            "Comédie musicale",
            "Guerre",
            "Documentaire",
            "Programme musical",
            "Comédie romantique",
            "Nature",
            "Kung Fu",
            "Emotion",
            "Japanimation",
            "Erotique",
            "Société et débats",
            "Autres séries",
            "Culture Gay et Lesbienne",
            "Concert",
            "Hard Rock / Métal",
            "Série Anime / OAV",
            "Amour & Romance",
            "Science et découvertes",
            "Variété française",
            "Péplum",
            "Humour",
            "Gore",
            "Voyages",
            "Ambiance & Relaxation",
            "Muet",
            "Mini-series / Feuilletons",
            "Série d'animation enfants",
            "Court métrage",
            "Opéra",
            "Musique Classique",
            "Beaux-Arts",
            "Conte",
            "Sport",
            "Culture urbaine",
            "Rap",
            "Variété internationale",
            "Fan Service",
            "Danse",
            "Sports mécaniques",
            "Jazz & Blues",
            "Bollywood",
            "Documentaire musical",
            "Culture",
            "Football",
            "Sitcom",
            "Theatre",
            "Documentaire-fiction",
            "Musiques du monde",
            "Série format court",
            "Catch",
            "Méthode",
            "Anime Yaoi",
            "R&B & Soul",
            "Santé & Bien-être",
            "Chasse & Pêche",
            "Cuisine, Jardinage & Déco",
            "Kickboxing & Freefight",
            "Jeux",
            "Divers",
            "Emissions TV",
            "Série documentaire",
            "Karaoke",
            "Musique de films",
            "Techno / Electro",
            "Telenovela"
        );

        foreach ($names as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
        }
        
        $names = array('DVD','Blu-Ray','Blu-Ray 3D');
        foreach($names as $name) {
            $format = new Format();
            $format->setName($name);
            $manager->persist($format);
        }
        
        $names = array('Marvel','Disney','Christopher Nolan','James Bond','Daniel Craig');
        foreach($names as $name) {
            $keyword = new Keyword();
            $keyword->setName($name);
            $manager->persist($keyword);
        }
        
        $manager->flush();
        
        $movie = new Movie();
        $movie->setTitle('Spectre');
        $movie->setOverview('Un message cryptique venu tout droit de son passé pousse Bond à enquêter...');
        $movie->setMovieDate(new \DateTime('2015-11-11'));
        $movie->setLink('http://www.allocine.fr/film/fichefilm_gen_cfilm=206892.html');
        $movie->setImage('http://www.dvdfr.com/images/dvd/covers/200x280/984d1472ba657352d3defe7eb7b75a48/160224/3d-007_spectre_br.0.jpg');
        $movie->setEan('3700259838542');
        $movie->setLend(false);
        $movie->setSee(false);
        $movie->setPurchaseDate(new \DateTime('2016-03-10'));
        
        $genre = $manager->getRepository('KriSpiXVideothequeBundle:Genre')->findOneByName('Action');
        $movie->addGenre($genre);
        
        $genre = $manager->getRepository('KriSpiXVideothequeBundle:Genre')->findOneByName('Aventure');
        $movie->addGenre($genre);
        
        $keyword = $manager->getRepository('KriSpiXVideothequeBundle:Keyword')->findOneByName('James Bond');
        $movie->addKeyword($keyword);
        
        $keyword = $manager->getRepository('KriSpiXVideothequeBundle:Keyword')->findOneByName('Daniel Craig');
        $movie->addKeyword($keyword);
        
        $format = $manager->getRepository('KriSpiXVideothequeBundle:Format')->findOneByName('Blu-Ray');
        $movie->setFormat($format);
        
        $manager->persist($movie);
        
        $manager->flush();
    }
}