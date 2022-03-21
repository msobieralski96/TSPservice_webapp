<!DOCTYPE html>
<html lang="pl-PL">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Etykieta {{ $SSCC_number }}</title>
        <style>
            @page {
                margin: 14px;
            }
            body {
                margin: 14px;
                font-family: DejaVu Sans;
            }
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
            }
        </style>
    </head>
    <body>
        <table style="width:100%">
            <caption><font size="1">Etykieta logistyczna</font></caption>
            <tr>
                <td colspan="4">
                    {{ $supplier_name }}<br>
                    <font size="2">{{ $supplier_address }}</font><br>
                    <font size="2">Tel: {{ $supplier_phone_number }}</font>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <font size="1">
                        <b>NADAWCA</b><br>
                        {{ $sender_name }}<br>
                        {{ $sender_address }}<br>
                        Tel: {{ $sender_phone_number }}
                    </font>
                </td>
                <td colspan="2">
                    <font size="1">
                        <b>ODBIORCA</b></br>
                        {{ $client_name }}<br>
                        {{ $client_address }}<br>
                        Tel: {{ $client_phone_number }}
                     </font>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <font size="1">
                        {{ $parcel_content }}
                    </font>
                </td>
                <td>
                    <font size="1">
                        Wymiary:<br>
                        {{ $size }}
                    </font>
                </td>
                <td>
                    <font size="1">
                        Masa:<br>
                        {{ $mass }}
                    </font>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <img src="{{ storage_path('app/barcodes/(00)'.$SSCC_number.'.png') }}">
                </td>
            </tr>
        </table>
    </body>
</html>
