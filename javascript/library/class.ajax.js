/*
 * This class is used  for managing ajax calls
 * 
 * @package xflextemplate
 * @author federico bernardin <federico@bernardin.it>
 * @ver 2.0
 * @state beta
 */

ajaxClass=function(parameters){
	//variabili private
	this._parameters = {
			type: "POST",
			url: "",
			data: "",
			async: false
	};
	//recupero i parametri dagli argomenti
	if (parameters.type) this._parameters.type = parameters.type;
	if (parameters.url) this._parameters.url = parameters.url;
	if (parameters.data) this._parameters.data = parameters.data;
	if (parameters.async) this._parameters.async = parameters.async;
	if (parameters.success) this._parameters.success = parameters.success;
}

ajaxClass.prototype=
{
	exec : function(){
		oThis = this;
		$.ajax({
			type: this._parameters.type,
			url: this._parameters.url,
			data: this._parameters.data,
			async: this._parameters.async,
			success: function(message){
				oThis._message = message;
				if (typeof(oThis._parameters.success) != "undefined")
					oThis._parameters.success(message);
			}
		});
		return oThis._message;
	}
}