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
        $listMoviesQuery = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMovies();
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $listMoviesQuery,
            $this->get('request')->query->get('page', $page),
            Movie::NB_PER_PAGE
        );

        return $this->render('KriSpiXVideothequeBundle:Movie:index.html.twig', array(
            'listMovies' => $pagination
        ));
    }
    
    public function viewAction(Movie $movie)
    {        
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
        $form = $this->createForm(new MovieEditType(), $movie);
        
        if ($form->handleRequest($request)->isValid()) {
            $keywords = $movie->getKeywords();
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
    
    public function menuGenreAction()
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

    public function searchRedirectAction(Request $request)
    {
        $searchTerm = $request->query->get('search');
        if (isset($searchTerm) AND $searchTerm != '') {
            return $this->redirect($this->generateUrl('krispix_videotheque_search', array(
                'searchTerm' => $searchTerm,
                'page' => 1
            )));
        } else {
            return $this->redirect($this->generateUrl('krispix_videotheque_home'));
        }
    }
    public function searchAction(Request $request, $searchTerm, $page)
    {     
        if (isset($searchTerm) AND $searchTerm != '') {   
            $em = $this->getDoctrine()->getManager();
            $listMovies = $em->getRepository("KriSpiXVideothequeBundle:Movie")
                ->getMoviesSearch($searchTerm);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $listMovies,
                $this->get('request')->query->get('page', $page),
                Movie::NB_PER_PAGE
            );
            return $this->render('KriSpiXVideothequeBundle:Movie:search.html.twig', array(
                'listMovies' => $pagination,
                'searchTerm' => $searchTerm,
            ));
        } else {
            return $this->redirect($this->generateUrl('krispix_videotheque_home'));
        }
    }
    
    public function newAction(Request $request, $page)
    {
        $listMoviesQuery = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getNewMovies();
      
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $listMoviesQuery,
            $this->get('request')->query->get('page', $page),
            Movie::NB_PER_PAGE
        );
        
        return $this->render('KriSpiXVideothequeBundle:Movie:new.html.twig', array(
            'listMovies' => $pagination
        ));
    }
    
    public function viewByGenreAction(Genre $genre, $page)
    {
        
        $listMoviesQuery = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMoviesWithGenre($genre);
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $listMoviesQuery,
            $this->get('request')->query->get('page', $page),
            Movie::NB_PER_PAGE
        );
        
        return $this->render('KriSpiXVideothequeBundle:Movie:genre.html.twig', array(
            'listMovies' => $pagination,
            'genre'      => $genre,
        ));
    }
    
    public function viewByKeywordAction(Keyword $keyword, $page)
    {
        
        $listMoviesQuery = $this->getDoctrine()
            ->getManager()
            ->getRepository('KriSpiXVideothequeBundle:Movie')
            ->getMoviesWithKeyword($keyword);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $listMoviesQuery,
            $this->get('request')->query->get('page', $page),
            Movie::NB_PER_PAGE
        );

        return $this->render('KriSpiXVideothequeBundle:Movie:keyword.html.twig', array(
            'listMovies' => $pagination,
            'keyword'    => $keyword,
        ));
    }
}