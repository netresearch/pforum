define(['jquery', 'TYPO3/CMS/Pforum/Datatables'], function ($, dataTables) {
  $( document ).ready( function() {
    oTable = $( "table.table" ).dataTable({
      "bJQueryUI": true,
      "sPaginationType": "full_numbers",
      "bProcessing": true,
      "bAutoWidth": false,
      "oLanguage": {
        "sLengthMenu": "Zeige _MENU_ Datensätze pro Seite",
        "sZeroRecords": "Keine Daten gefunden",
        "sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Datensätzen",
        "sSearch": "Suche",
        "sInfoEmpty": "Zeige 0 bis 0 von 0 Datensätzen",
        "sInfoFiltered": "(gefiltert von _MAX_ Datensätzen insgesamt)",
        "oPaginate": {
          "sFirst": "Erste",
          "sPrevious": "Vorherige",
          "sNext": "Nächste",
          "sLast": "Letzte"
        }
      },
      "iDisplayLength": 25,
      "aaSorting": [[ 0, "asc" ]],
      "aoColumnDefs": [
        { "bSortable": false, "bSearchable": false, "sWidth": "100px", "aTargets": [ 2 ] }
      ]
    });
    
    // hide description col
    oTable.fnSetColumnVis( 1, false );
    
    // show/hide description by click on title
    $( "table.table tbody tr td:eq(0)").on("click", function () {
      var nTr = $( this ).parents( "tr" )[0];
      if ( oTable.fnIsOpen(nTr) ) {
        oTable.fnClose( nTr );
      } else {
        var aData = oTable.fnGetData( nTr );
        oTable.fnOpen( nTr, "<table width=\"100%\"><tr><td>" + aData[1] + "</td></tr></table>", "details" );
      }
    });
  });
});