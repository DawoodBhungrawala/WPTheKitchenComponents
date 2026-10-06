

	function randomHashKey(length) {
		var chars = "abcdefghijklmnopqrstuvwxyz!@ABCDEFGHIJKLMNOP1234567890";
		var hashkey = "";
		for (var x = 0; x < length; x++) {
			var i = Math.floor(Math.random() * chars.length);
			hashkey += chars.charAt(i);
		}
		return hashkey;
	}
	function changeKey() {
		document.getElementById('secret_key').value = randomHashKey(55);
	}

	function copyToClipboard(element) {
	  var copyText = document.getElementById(element);
	  copyText.select();
	  copyText.setSelectionRange(0, 99999);
	  document.execCommand("copy");
	}
	
	jQuery(function($){
		
		$('#synchronization_schedule').on('change', function() {
			
			$(this).closest('tr').siblings('tr').hide();
			$('.external-scheduler').hide();
			
			if ($(this).val() !== '0') {
				
				$(this).closest('tr').siblings('tr').show();
			}
			
			if ($(this).val() === 'external') {
			
				$('.external-scheduler').show();
			}
		});
		
		$('#synchronization_stock').on('change', function() {
			
			$('.synchronization_stock_element').hide();
			
			if ($(this).is(':checked')) {
				
				$('.synchronization_stock_element').show();
			}
		});
		
		$('#synchronization_schedule, #synchronization_stock').trigger('change');
		
		$('[data-target]').on('click', function() {
			
			$(this).toggleClass('active');
			
			$('[data-target-content="' + $(this).data('target') + '"]').slideToggle();
		});
		
		
	});



