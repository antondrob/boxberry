	function changeCityValue(){
		var custom_city = document.getElementById('billing_city').value;
		return custom_city;
	}
	function encryptedToken(){
		var encryptedToken = document.getElementById('encrypted-token').innerHTML;
		return encryptedToken;
	}
	function callback_function(result){
		document.getElementById('order_comments').value = "Детали доставки:\n" + "Город: " + result.name + "\n" + "Адрес ПВЗ: " + result.address + "\n" + "График работы: " + result.workschedule + "\n" + "Номер телефона ПВЗ: " + result.phone + "\n" + "Стоимость: " + result.price + " руб." + "\n" + "Примерный срок доставки: " + result.period + " дн.";
		if (result.prepaid=='Yes') { 
		document.getElementById('order_comments').value += "\nОтделение работает только по предоплате!";
		}
document.getElementById('billing_city').value = result.name;
		var input_parameters = {
			"calc": "1",
			"select_office": "1",
			};
	}
	jQuery( document ).ajaxComplete(function() {
		function loadScript(url, callback){
	
    // Добавляем тег сценария в body – как и предлагалось выше
		var body = document.getElementsByTagName('body')[0];
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = url;

    // Затем связываем событие и функцию обратного вызова.
    // Для поддержки большинства обозревателей используется несколько событий.
		script.onreadystatechange = callback;
		script.onload = callback;

    // Начинаем загрузку
		body.appendChild(script);
}
loadScript("https://points.boxberry.de/js/boxberry.js");
		var boxberryShipping = document.getElementById('shipping_method_0_boxberry_shipping_method');
		var boxberryShippingLabel = boxberryShipping.nextElementSibling;
		var liBoxberryShipping = boxberryShipping.parentNode;
		var link = document.createElement('a');
		link.innerHTML = 'Выбрать ПВЗ';
		link.setAttribute('href','#');
		link.setAttribute("onclick","boxberry.open('callback_function', encryptedToken(), changeCityValue(),'', 3000,1000,3000,20,20,20); return false;");
		liBoxberryShipping.insertBefore(link, boxberryShippingLabel.nextElementSibling);
	});