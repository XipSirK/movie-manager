/*!
 * JavaScript Cookie v2.1.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		var _OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = _OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init (converter) {
		function api (key, value, attributes) {
			var result;

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				return (document.cookie = [
					key, '=', value,
					attributes.expires && '; expires=' + attributes.expires.toUTCString(), // use expires attribute, max-age is not supported by IE
					attributes.path    && '; path=' + attributes.path,
					attributes.domain  && '; domain=' + attributes.domain,
					attributes.secure ? '; secure' : ''
				].join(''));
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var name = parts[0].replace(rdecode, decodeURIComponent);
				var cookie = parts.slice(1).join('=');

				if (cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.get = api.set = api;
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));
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