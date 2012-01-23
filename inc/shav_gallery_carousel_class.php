<?php
/** @class SHAV_jCarouselGallery
 *	@brief Класс галереии.
 *	Пример использования:
 *	@code
 * $gallery = new SHAV_jCarouselGallery();
 * $gallery->idGallery = 'gallery';
 * $gallery->idContainer = 'conteiner_gallery';
 * $gallery->title = 'Simple photo gallery with Galleria and jCarousel.';
 * $gallery->images = array();
 * $i = 1;
 * while($i < 10)
 * {
 *	 $gallery->images[] = array('image'=>'images/Battles/Battles_0'.($i+1).'.jpg', 'title'=>'Image '.$i, 'thumbs'=>'images/Battles/Battles_0'.($i+1).'.jpg');
 *	 $i++;
 * }
 * $gallery->size_thumbs = array('width'=>'auto', 'height'=>50);
 * $gallery->size = array('width'=>300, 'height'=>100);
 * $gallery->drawGallery(true);
 *	@endcode
 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
 *	@date Созданно: 06.02.2010
 *	@date Обновленно: 05.02.2011*/
class SHAV_jCarouselGallery extends SHAV_Object
{
	/** Идентификатор галереи.*/
	public $idGallery = 'gallery';
	
	/** Идентификатор контейнера галереи.*/
	public $idContainer = 'conteiner_gallery';
	
	/** Загаловок галереи.*/
	public $title = 'Simple photo gallery with Galleria and jCarousel.';
	
	/** Массив картинок для отображения в галереи.
	 *	@code
	 *	array('image'=>'', 'title'=>'', 'thumbs'=>'')
	 *	@endcode
	 */
	public $images = array();
	
	/** Размер картинов в режиме предварительного просмотра.*/
	public $size_thumbs = array('width'=>'auto', 'height'=>50);
	
	/** Размер картинок.*/
	public $size = array('width'=>500, 'height'=>300);

	/** Конструктор класса
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function SHAV_jCarouselGallery(){}

	/** Создает JavaScript настройку галереи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.02.2010
	 *	@date Обновленно: 05.02.2011*/
	private function create_JSConfig()
	{
		global $shavJS;
		$shavJS->content .= "$(function(){
			$('#".$this->idGallery." li').each(function(idx) {
				$(this).data('index', (++idx));
		});

		$('#".$this->idGallery."').jcarousel({
			scroll: 5,
			initCallback: initCallbackFunction
		})

		function initCallbackFunction(carousel) {
			$('#img').bind('image-loaded',function() {
				public idx =  $('#gallery li.active').data('index') - 2;

				carousel.scroll(idx);
				return false;
		});

		// hotkeys plugin: use arrows to control the gallery
		$(document).bind('keydown', 'right', function (evt){ $.galleria.next(); });
		$(document).bind('keydown', 'left', function (evt){ $.galleria.prev(); });
		$(document).bind('keydown', 'up', function (evt){ $('.jcarousel-next-horizontal').click(); return false; });
		$(document).bind('keydown', 'down', function (evt){ $('.jcarousel-prev-horizontal').click(); return false; });
		};

		// load and fade-in thumbnails
		$('#".$this->idGallery." li img').css('opacity', 0).each(function() {
			if (this.complete || this.readyState == 'complete') { $(this).animate({'opacity': 1}, 300) }
			else { $(this).load(function() { $(this).animate({'opacity': 1}, 300) }); }
		});


		$('#".$this->idGallery."').galleria({
			// #img is the empty div which holds full size images
			insert: '#img',

			// enable history plugin
			history: true,

			// function fired when the image is displayed
			onImage: function(image, caption, thumb) {
				// fade in the image
				image.hide().fadeIn(500);

				// animate active thumbnail's opacity to 1, other list elements to 0.6
				thumb.parent().fadeTo(200, 1).siblings().fadeTo(200, 0.6)

				// $('#img').data('currentIndex', $li.data('index')).trigger('image-loaded')

				$('#img')
				.trigger('image-loaded')
				.hover(
				function(){ $('#img .caption').stop().animate({height: 50}, 250) },
				function(){
					if (!$('#show-caption').is(':checked')) {
						$('#img .caption').stop().animate({height: 0}, 250)
		}
		}
		);
		},

		// function similar to onImage, but fired when thumbnail is displayed
		onThumb: function(thumb) {
			public $li = thumb.parent(),
			opacity = $li.is('.active') ? 1 : 0.6;

			// hover effects for list elements
			$li.hover(
			function() { $li.fadeTo(200, 1); },
			function() { $li.not('.active').fadeTo(200, opacity); }
			)
		}
		}).find('li:first').addClass('active') // display first image when Galleria is loaded

		$('#img .caption').css('height', 0)

		$('#slideshow').hide()

		// this one is for Firefox, which loves to leave fields checked after page refresh
		$('#toggle-slideshow, #show-caption').removeAttr('checked')

		$('#show-caption').change(function(){
			if (this.checked) {
				$('#img .caption').stop().animate({height: 50}, 250)
		} else {
			$('#img .caption').stop().animate({height: 0}, 250)
		}
		})


		public slideshow,
		slideshowPause =  $('#slideshow-pause').val()

		$('#slideshow-pause').change(function(){
			slideshowPause = this.value

			// clear interval when timeout is changed
			window.clearInterval(slideshow)

			// and set new interval with new timeout value
			slideshow = window.setInterval(function(){
				$.galleria.next()
		}, slideshowPause * 1000) // must be set in milisecond
		})

		$('input#toggle-slideshow').change(function(){
			if (this.checked) {
				$('#slideshow').fadeIn()

				// set interval when slideshow is enabled
				slideshow = window.setInterval(function(){
					$.galleria.next()
		}, slideshowPause * 1000)
		} else {
			$('#slideshow').fadeOut()

			// clear interval when slideshow if disabled
			window.clearInterval(slideshow)
		}
		})
		});";
	}

	/** Выводит HTML-код галлерии.
	 *	@param $isDraw - выводить или нет html-код галереи.
	 *	@author Andrew Shapovalov <a href="http://lmwshav.org.ua">Сайт</a>
	 *	@date Созданно: 06.02.2010
	 *	@date Обновленно: 05.02.2011*/
	function drawGallery($isDraw = false)
	{
		global $shavJS;
		$this->createJavaScript();
		
		$shavJS->content .= '<style>';
		$shavJS->content .= '#conteiner_gallery {
			width: '.$this->size['width'].'px;
			margin: 0 auto;
			padding: 20px;
			background-color: #E5EDF2;
		}

		#img {
			position: relative;
			width: '.$this->size['width'].'px;
			height: '.$this->size['height'].'px;
			margin-bottom: 1em;
		}

		/*#'.$this->idGallery.' img
		{
			width:100px;
		}*/

		.jcarousel-skin-tango .jcarousel-container-horizontal {
			width: '.($this->size['width']-140).'px;
		}

		#'.$this->idGallery.' { height: 84px; }

		#conteiner_gallery, #img, #'.$this->idGallery.' li {
			border: 1px solid #223106;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
		}

		#'.$this->idGallery.' li {
			float: left;
			padding: 1px;
			border-color: #345;
			cursor: pointer;
		}';
		$shavJS->content .= '</style>';
		$content  = '<div id="conteiner_gallery">';
		$content .= '<h1>'.$this->title.'</h1>';
		$content .= '<div id="img"></div>';
		$content .= '<ul id="gallery" class="jcarousel-skin-tango">';
		foreach($this->images as $image)
		{
			$content .= '<li><a href="'.$image['image'].'" title="'.$image['title'].'"><img src="'.$image['thumbs'].'" width="'.$this->size_thumbs['width'].'" height="'.$this->size_thumbs['height'].'" alt="" /></a></li>';
		}
		$content .= '</ul>';
		$content .= '</div>';

		if($isDraw == true)
			echo $content;
		else
			return $content;
	}
}
?>