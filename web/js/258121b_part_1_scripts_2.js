$(document).ready(function() {

    $('#list').click(function(event){
      event.preventDefault();
      $('#movies .item').addClass('list-group-item');
      $('#movies .thumbnail').addClass('thumbnail-list');
      $('#movies hr').hide();
      $('#movies .overview').show();
      $('#filter-view').val('list');
    });
    $('#grid').click(function(event){
      event.preventDefault();
      $('#movies .item').removeClass('list-group-item');
      $('#movies .item').addClass('grid-group-item');
      $('#movies .thumbnail').removeClass('thumbnail-list');
      $('#movies hr').show();
      $('#movies .overview').hide();
      $('#filter-view').val('grid');
    });

    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    var $container = $('div#krispix_videothequebundle_movie_keywords');

    // On ajoute un lien pour ajouter une nouvelle catégorie
    var $addLink = $('<a href="#" id="add_keyword" class="btn btn-default">Ajouter un mot clé</a>');
    $container.append($addLink);

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addLink.click(function(e) {
        addKeyword($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find(':input').length;

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (index == 0) {
      //addKeyword($container);
    } else {
      // Pour chaque catégorie déjà existante, on ajoute un lien de suppression
      $container.children('div').each(function() {
        addDeleteLink($(this));
      });
    }

    // La fonction qui ajoute un formulaire 
    function addKeyword($container) {
      // Dans le contenu de l'attribut « data-prototype », on remplace :
      // - le texte "__name__label__" qu'il contient par le label du champ
      // - le texte "__name__" qu'il contient par le numéro du champ
      var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'Mot clé n°' + (index+1))
          .replace(/__name__/g, index));

      // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
      addDeleteLink($prototype);

      // On ajoute le prototype modifié à la fin de la balise <div>
      $container.append($prototype);

      // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
      index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype) {
      // Création du lien
      $deleteLink = $('<a href="#" class="btn btn-danger">X</a>');
      // Ajout du lien
      $prototype.find('label').empty().append($deleteLink);
      $prototype.find('label').addClass('label-delete')
      
      // Ajout du listener sur le clic du lien
      $deleteLink.click(function(e) {
        $prototype.remove();
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
      });
    }
    
    var $ean = $('#krispix_videothequebundle_movie_ean');
    $ean.on('input', function() {
        if ($ean.val().length == 13) {
          var $form = $(this).closest('form');
          $.ajax({
              url: $form.attr('action'),
              //url: $("#path").attr('data-path'),
              type: $form.attr('method'),
              data: {'ean': $ean.val()},
              dataType: 'json',
              success: function(data) {
                var movie = $.parseJSON(data);                    
                $('#krispix_videothequebundle_movie_title').val(movie.title);
                $('#krispix_videothequebundle_movie_link').val(movie.url);
                $('#krispix_videothequebundle_movie_overview').val(movie.overview);
                $('#krispix_videothequebundle_movie_image').val(movie.image);
                // Date du film
                splitDate = movie.movieDate.split("-");
                $('#krispix_videothequebundle_movie_movieDate_day').val(parseInt(splitDate[2]));
                $('#krispix_videothequebundle_movie_movieDate_month').val(parseInt(splitDate[1]));
                $('#krispix_videothequebundle_movie_movieDate_year').val(parseInt(splitDate[0]));
                // Format
                $('#krispix_videothequebundle_movie_format').find('*').filter(function() {
                    return $(this).text() === movie.format;
                }).attr('selected','selected');
                // Keywords
                var $keywords = $('div#krispix_videothequebundle_movie_keywords');
                $.each(movie.keywords, function(index, value) {
                  addKeyword($keywords);
                  $('#krispix_videothequebundle_movie_keywords_'+index+'_name').val(value);
                });
                // Genres
                $.each(movie.genres, function(index, value) {
                  $('#krispix_videothequebundle_movie_genres').find('*').filter(function() {
                    return $(this).text() === value;
                  }).attr('selected','selected');
                });
              }
          });
        }
    });
});