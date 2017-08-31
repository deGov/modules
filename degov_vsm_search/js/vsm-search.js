jQuery(document).ready(function() {
  var vsmSearchSelect = jQuery('#block-search-content-section').find('select');
  var query = jQuery('#edit-volltext').val();
  vsmSearchSelect.append('<option value="/suche/vsm?volltext=' + query + '">Verwaltungssuchmaschine NRW</option>');
});

