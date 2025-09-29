(function(){
	function on(selector, event, handler){
		document.addEventListener(event, function(e){
			var el = e.target.closest(selector);
			if (el) handler(e, el);
		});
	}

	function api(path, options){
		options = options || {};
		options.headers = options.headers || {};
		if (window.wpApiSettings && window.wpApiSettings.nonce) {
			options.headers['X-WP-Nonce'] = wpApiSettings.nonce;
		}
		var base = (window.wpApiSettings && window.wpApiSettings.root) ? wpApiSettings.root : '/wp-json/';
		return fetch(base + NirmanNeedsAdmin.namespace + '/' + path, options);
	}

	on('.projected-price-form .save-projected-price', 'click', function(e, btn){
		var wrap = btn.closest('.projected-price-form');
		var body = {
			product_id: wrap.dataset.productId,
			is_variation: wrap.dataset.isVariation === '1',
			projected_price: wrap.querySelector('input[name="projected_price"]').value,
			start: wrap.querySelector('input[name="start_date"]').value
		};
		btn.disabled = true;
		api('projected-prices', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(body)
		}).then(function(res){
			btn.disabled = false;
			if (res && res.ok) { location.reload(); }
			else { res.json().then(function(j){ alert(j && j.message ? j.message : 'Error'); }); }
		});
	});

	on('.projected-prices-table .delete-projected-price', 'click', function(e, btn){
		if (!confirm(NirmanNeedsAdmin.confirmDelete)) return;
		var id = btn.dataset.id;
		btn.disabled = true;
		api('projected-prices/' + id, { method: 'DELETE' }).then(function(res){
			btn.disabled = false;
			if (res && res.ok) { location.reload(); }
			else { res.json().then(function(j){ alert(j && j.message ? j.message : 'Error'); }); }
		});
	});
})();


