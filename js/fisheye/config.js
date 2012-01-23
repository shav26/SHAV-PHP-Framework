  jQuery(function($){
/*********************
** JQDOCK SETUPS... **
*********************/
    //make images that are not enclosed in anchors clickable...
    //(some will simply reload the current page, but then some of the links will do that anyway, so...)
    $('div.demo>img').each(function(i){
        $(this).one('click', function(){ location.href = 'index.php?dt='+(i%5); return false; });
      });
    //apply jqDock to each of the demo menus, setting varying options for each one...
    $('div.demo').each(function(i){ //opts updated so as not to use null as 'don't override default' - jQuery v1.2.5 changed extend() to not ignore nulls!
        var opts = { align:        [ 'bottom', 'right', 'top' , 'middle', 'left', 'center' ][i] || 'bottom' //default
                   , size:         [  48     ,  48    ,  48   ,  48     ,  36   ,  60      ][i] || 36       //default
                   , distance:     [  60     ,  60    ,  60   ,  60     ,  48   ,  80      ][i] || 54       //default
                   , coefficient : [  1.5    ,  1.5   ,  1.5  ,  1      ,  1.5  ,  1.5     ][i] || 1.5      //default
                   , labels:       [  true   ,  'mc'  ,  true ,  'br'   ,  true ,  false   ][i] || false    //default
                   , duration:     500 //default
                     //for menu1 and menu7, set a function to use the 'alt' attribute if present, else construct a PNG path from the 'src'...
                   , source:       (i==0 || i==6) ? function(i){ return (this.alt) ? false : this.src.replace(/(jpg|gif)$/,'png'); } : false //default
                   };
        $(this).jqDock(opts);
      });
    //handle clicking label for images within links :
    //if labels are enabled, and you have images within links, clicking on the label
    //will do nothing!
    //in order to get the link to activate you will need to provide some sort of click
    //handler on the image (which jqDock will trigger) - if there is not one already?
    //for example, on everything except #menu1, I will apply the following click handler
    //to all my images-within-links...
    //(ie. clicking a label on an image-within-a-link on #menu1 will still do nothing!)
    $('div.demo a>img').not($('#menu1 a>img')).bind('click', function(){
        var Href = $(this).parent().get(0).href;
        //I don't have any hrefs that are not straightforward links, but for the sake of completeness,
        //one could take this as being indicative of the likelihood that there is no click handler...
        if(Href && !/^javascript:/i.test(Href)){ //change location...
          location.href = Href;
        }else{ //trigger a click handler?...
          $(this).parent().trigger('click');
        }
        return false;
      });
  });
