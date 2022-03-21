@extends('master')

@section('datatablescripts')
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#parcelstable thead tr').clone(true).appendTo( '#parcelstable thead' );
            $('#parcelstable thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" class="filters" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            var table = $('#parcelstable').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "wszystkie"]],
                "language": {
                    "decimal": "",
                    "emptyTable": "Brak informacji w tabeli",
                    "info": "Wyświetla od _START_ do _END_ z _TOTAL_ przesyłek",
                    "infoEmpty": "Nie znaleziono żadnych przesyłek",
                    "infoFiltered": "(przefiltrowano wszystkie _MAX_ przesyłek)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Wyświetlaj _MENU_ przesyłek na stronę",
                    "loadingRecords": "Wczytywanie danych...",
                    "processing": "Przetwarzanie...",
                    "search": "Wyszukaj:",
                    "zeroRecords": "Nie znaleziono pasujących przesyłek",
                    "paginate": {
                        "first": "Pierwszy",
                        "last": "Ostatni",
                        "next": "Następny",
                        "previous": "Poprzedni"
                    },
                    "aria": {
                        "sortAscending": ": aktywuj, by posortować kolumny rosnąco",
                        "sortDescending": ": aktywuj, by posortować kolumny malejąco"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#tsptable thead tr').clone(true).appendTo( '#tsptable thead' );
            $('#tsptable thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" class="filters" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            var table = $('#tsptable').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                "lengthMenu": [[-1], ["wszystkie"]],
                "language": {
                    "decimal": "",
                    "emptyTable": "Brak informacji w tabeli",
                    "info": "Wyświetla od _START_ do _END_ z _TOTAL_ przesyłek",
                    "infoEmpty": "Nie znaleziono żadnych przesyłek",
                    "infoFiltered": "(przefiltrowano wszystkie _MAX_ przesyłek)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Wyświetlaj _MENU_ przesyłek na stronę",
                    "loadingRecords": "Wczytywanie danych...",
                    "processing": "Przetwarzanie...",
                    "search": "Wyszukaj:",
                    "zeroRecords": "Nie znaleziono pasujących przesyłek",
                    "paginate": {
                        "first": "Pierwszy",
                        "last": "Ostatni",
                        "next": "Następny",
                        "previous": "Poprzedni"
                    },
                    "aria": {
                        "sortAscending": ": aktywuj, by posortować kolumny rosnąco",
                        "sortDescending": ": aktywuj, by posortować kolumny malejąco"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#ordertable thead tr').clone(true).appendTo( '#ordertable thead' );
            $('#ordertable thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" class="filters" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            var table = $('#ordertable').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                "lengthMenu": [[-1], ["wszystkie"]],
                "language": {
                    "decimal": "",
                    "emptyTable": "Brak informacji w tabeli",
                    "info": "Wyświetla od _START_ do _END_ z _TOTAL_ przesyłek",
                    "infoEmpty": "Nie znaleziono żadnych przesyłek",
                    "infoFiltered": "(przefiltrowano wszystkie _MAX_ przesyłek)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Wyświetlaj _MENU_ przesyłek na stronę",
                    "loadingRecords": "Wczytywanie danych...",
                    "processing": "Przetwarzanie...",
                    "search": "Wyszukaj:",
                    "zeroRecords": "Nie znaleziono pasujących przesyłek",
                    "paginate": {
                        "first": "Pierwszy",
                        "last": "Ostatni",
                        "next": "Następny",
                        "previous": "Poprzedni"
                    },
                    "aria": {
                        "sortAscending": ": aktywuj, by posortować kolumny rosnąco",
                        "sortDescending": ": aktywuj, by posortować kolumny malejąco"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#userstable thead tr').clone(true).appendTo( '#userstable thead' );
            $('#userstable thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" class="filters" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            var table = $('#userstable').DataTable({
                orderCellsTop: true,
                fixedHeader: false,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "wszyscy"]],
                "language": {
                    "decimal": "",
                    "emptyTable": "Brak informacji w tabeli",
                    "info": "Wyświetla od _START_ do _END_ z _TOTAL_ użytkowników",
                    "infoEmpty": "Nie znaleziono żadnych użytkowników",
                    "infoFiltered": "(przefiltrowano wszystkie _MAX_ użytkowników)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Wyświetlaj _MENU_ użytkowników na stronę",
                    "loadingRecords": "Wczytywanie danych...",
                    "processing": "Przetwarzanie...",
                    "search": "Wyszukaj:",
                    "zeroRecords": "Nie znaleziono pasujących użytkowników",
                    "paginate": {
                        "first": "Pierwszy",
                        "last": "Ostatni",
                        "next": "Następny",
                        "previous": "Poprzedni"
                    },
                    "aria": {
                        "sortAscending": ": aktywuj, by posortować kolumny rosnąco",
                        "sortDescending": ": aktywuj, by posortować kolumny malejąco"
                    }
                }
            });
        });
    </script>
@stop
