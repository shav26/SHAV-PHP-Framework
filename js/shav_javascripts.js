/** Функция для того, чтобы прятать или показывать содержимое компонента
ПАРАМЕТРЫ:
	idContent - id или class компонента, который нужно показывать или прятать.
РЕЗУЛЬТАТ:
	Показывает прячет содержимое компонента с idContent*/
function componentSlick(idContent)
{
	$(idContent).toggle(400);
	return false;
}

/*Функция для показа подробного текста.
ПАРАМЕТРЫ:
	str - id компонента в который выводится полный текст;
	content_small - короткий текст (часть основного);
	content_full - основной текст;
	linkTitle - название ссылки поумолчанию;
	showText - текст, который будет присвоен ссылки после, второго нажатия на нее;
	hideText - текст, который будет у ссылки для того, чтобы спрутать текст.
РЕЗУЛЬТАТ:
	Выводит весть или часть текста.*/
function hideDiv(str, content_small, content_full, linkTitle, showText, hideText)
{
	showText = typeof(showText) != 'undefined' ? showText : '[show]';
	hideText = typeof(hideText) != 'undefined' ? hideText : '[hide]';

	if(linkTitle == showText)
		linkTitle = hideText;
	else
		linkTitle = showText;

	$(str).html(content_full+" <a href=\"#\" onClick=\"return hideDiv('"+str+"', '"+content_full+"', '"+content_small+"','"+linkTitle+"', '"+showText+"', '"+hideText+"');\">"+linkTitle+"</a>");
	return false;
}

