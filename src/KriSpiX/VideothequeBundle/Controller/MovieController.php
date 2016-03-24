<?php

namespace KriSpiX\VideothequeBundle\Controller;

use KriSpiX\VideothequeBundle\Entity\Movie;
use KriSpiX\VideothequeBundle\Entity\Genre;
use KriSpiX\VideothequeBundle\Entity\Keyword;
use KriSpiX\VideothequeBundle\Form\MovieType;
use KriSpiX\VideothequeBundle\Form\MovieEditType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class MovieController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException("La page ".$page." n'existe pas !");
        }
        $nbPerPage = Movie::NB_PER_PAGE;
        
        $listMovies = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMovies($page, $nbPerPage);
        
        $nbPages = ceil(count($listMovies)/$nbPerPage);
        
        if ($page > $nbPages) {
            //throw new NotFoundHttpException("La page ".$page." n'existe pas !");
        }
        
        return $this->render('KriSpiXVideothequeBundle:Movie:index.html.twig', array(
            'listMovies' => $listMovies,
            'nbPages'    => $nbPages,
            'page'       => $page,
        ));
    }
    
    public function viewAction(Movie $movie)
    {
        /*$repo = $this->getDoctrine()->getManager()->getRepository('KriSpiXVideothequeBundle:Movie');
        $movie = $repo->find($id);
        
        if (null === $movie) {
            throw new NotFoundHttpException("Le movie d'id ".$id." n'existe pas !");
        }*/
        
        return $this->render('KriSpiXVideothequeBundle:Movie:view.html.twig', array(
            'movie' => $movie
        ));
    }
    
    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function ajaxAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $ean = $request->get('ean');
            $title = 'coucou';
            $json = json_encode(array(
                'title' => $title
            ));
            
            return new JsonResponse($json);
        }
    }
    
    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function addAction(Request $request)
    {
        /*$antispam = $this->container->get('kri_spi_x_videotheque.antispam');
        $text = 'Avatar';
        if ($antispam->isSpam($text)) {
            throw new \Exception('Votre message est un spam !');
        }*/
        $movie = new Movie();
        
        if($request->isXmlHttpRequest()) {
            $ean = $request->get('ean');
            $dvdfrApi = $this->container->get('kri_spi_x_videotheque.dvdfr.api');
            $dvd = $dvdfrApi->searchEan($ean);        

            //$movie->setMovieDate(\DateTime::createFromFormat('Y-m-d', $dvd['movieDate']));
            if ($dvd['format'] == 'BRD') {
                $dvd['format'] = 'Blu-Ray';
            } elseif($dvd['format'] == 'BRD-3D') {
                $dvd['format'] = 'Blu-Ray 3D';
            }
            $json = json_encode(array(
                'title'     => $dvd['title'],
                'url'       => $dvd['url'],
                'overview'  => $dvd['overview'],
                'movieDate' => $dvd['movieDate'],
                'image'     => $dvd['image'],
                'format'    => $dvd['format'],
                'genres'    => $dvd['genres'],
                'keywords'  => $dvd['keywords'],
            ));
            
            return new JsonResponse($json);
        }
        
        $form = $this->createForm(new MovieType(), $movie);
        
        $form->handleRequest($request);
        
        if($form->isValid() && $request->isMethod('POST')) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
                
                $request->getSession()->getFlashBag()->add('notice', 'Film bien enregistré.');
                return $this->redirect($this->generateUrl('krispix_videotheque_view', array('slug' => $movie->getSlug())));
            }
            catch (Exception $e) {
                $request->getSession()->getFlashBag()->add('error', 'Erreur.');
                return $this->redirect($this->generateUrl('krispix_videotheque_home'));
            }
        }
        
        return $this->render('KriSpiXVideothequeBundle:Movie:add.html.twig', array(
            'form' => $form->createView()
        ));
        
    }
    
    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function editAction(Movie $movie, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*$movie = $em->getRepository('KriSpiXVideothequeBundle:Movie')->find($id);
        
        if(null === $movie) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !");
        }*/
        
        $form = $this->createForm(new MovieEditType(), $movie);
        
        if ($form->handleRequest($request)->isValid()) {
            $keywords = $movie->getKeywords();
            //print_r($keywords);
            //die;
            foreach($keywords as $keyword){
                $results = $em->getRepository('KriSpiXVideothequeBundle:Keyword')->findBy(array('name' => $keyword->getName()), array('id' => 'ASC'));
                if (count($results) > 1){
                    
                    $em->clear($results[0]);
                }
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Film bien modifié.');
            return $this->redirect($this->generateUrl('krispix_videotheque_view', array('slug' => $movie->getSlug())));
        }
        
        return $this->render('KriSpiXVideothequeBundle:Movie:edit.html.twig', array(
            'form'  => $form->createView(),
            'movie' => $movie
        ));
    }
    
    /**
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function deleteAction(Movie $movie, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*$movie = $em->getRepository('KriSpiXVideothequeBundle:Movie')->find($id);
        
        if(null === $movie) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !");
        }*/
        
        $form = $this->createFormBuilder()->getForm();        
        
        if ($form->handleRequest($request)->isValid()) {
            $em->remove($movie);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add('info', 'Film bien supprimée.');
            return $this->redirect($this->generateUrl('krispix_videotheque_home'));
        }

        return $this->render('KriSpiXVideothequeBundle:Movie:delete.html.twig', array(
            'movie' => $movie,
            'form'  => $form->createView()
        ));
    }
    
    public function menuGenreAction($limit = 3)
    {        
        $listMovies = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMoviesGenres();
            
        $tabGenres = array();
        $tabAlreadyParse = array();
        foreach($listMovies as $movie) {
            $genres = $movie->getGenres();
            foreach($genres as $genre) {
                if(!in_array($genre, $tabAlreadyParse)) {
                    $tabGenres[] = $genre;
                    $tabAlreadyParse[] = $genre;
                }
            }
        }
                
        return $this->render('KriSpiXVideothequeBundle:Movie:menuGenre.html.twig', array(
            'listGenres' => $tabGenres
        ));
    }

    public function searchAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search');
            $em = $this->getDoctrine()->getManager();
            $listMovies = $em->getRepository("KriSpiXVideothequeBundle:Movie")
                ->getMoviesSearch($searchTerm);

            return $this->render('KriSpiXVideothequeBundle:Movie:search.html.twig', array(
                'listMovies' => $listMovies,
                'searchTerm' => $searchTerm,
            ));
        } else {
            return $this->redirect($this->generateUrl('krispix_videotheque_home'));
        }
    }
    
    public function newAction($limit = 4)
    {
        $listMovies = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->findBy(
                array(),                 // Pas de critère
                array('purchaseDate' => 'desc'), // On trie par date décroissante
                $limit,                  // On sélectionne $limit annonces
                0                        // À partir du premier
        );
        
        return $this->render('KriSpiXVideothequeBundle:Movie:new.html.twig', array(
            'listMovies' => $listMovies
        ));
    }
    
    public function viewByGenreAction(Genre $genre)
    {
        
        $listMovies = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMoviesWithGenre($genre);
        
        return $this->render('KriSpiXVideothequeBundle:Movie:genre.html.twig', array(
            'listMovies' => $listMovies,
            'genre'      => $genre,
        ));
    }
    
    public function viewByKeywordAction(Keyword $keyword)
    {
        
        $listMovies = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMoviesWithKeyword($keyword);

        return $this->render('KriSpiXVideothequeBundle:Movie:keyword.html.twig', array(
            'listMovies' => $listMovies,
            'keyword'    => $keyword,
        ));
    }
}